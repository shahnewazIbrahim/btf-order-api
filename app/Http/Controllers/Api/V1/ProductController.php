<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Jobs\ImportProductsFromCsv;
use App\Models\Product;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service,
        protected ProductRepository $products
    ) {}

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
        ];

        $perPage = (int) $request->query('per_page', 15);

        $user = auth('api')->user();

        // Admin সব দেখতে পারবে, Vendor শুধু নিজের
        if ($user && ($user->role->name ?? null) === 'Vendor') {
            $filters['vendor_id'] = $user->id;
        }

        $items = $this->products->paginated($filters, $perPage);

        return ProductResource::collection($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'      => 'nullable|image|max:2048',
            'base_price' => 'required|numeric|min:0',
            'is_active'  => 'boolean',
        ]);

        $data['user_id'] = $request->user('api')->id;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = $this->service->create($data);

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load('variants'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($request->user('api'), $product);

        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image'      => 'nullable|image|max:2048',
            'base_price' => 'sometimes|numeric|min:0',
            'is_active'  => 'sometimes|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = $this->service->update($product, $data);

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct(auth('api')->user(), $product);

        $this->service->delete($product);

        return response()->json(null, 204);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $user = auth('api')->user();

        // শুধু Admin/Vendor allowed (role middleware থাকলে আরও ভালো)
        if (! in_array($user->role->name ?? '', ['Admin', 'Vendor'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $path = $request->file('file')->store('imports');

        ImportProductsFromCsv::dispatch($path, $user->id);

        return response()->json([
            'message' => 'Import queued. Products will be created in background.',
        ], 202);
    }

    protected function authorizeProduct(?User $user, Product $product): void
    {
        if (! $user || ! $user->role) {
            abort(403);
        }

        $role = $user->role->name;

        if ($role === 'Admin') {
            return;
        }

        if ($role === 'Vendor' && $product->user_id === $user->id) {
            return;
        }

        abort(403);
    }
}
