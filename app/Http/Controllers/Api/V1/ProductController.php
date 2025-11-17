<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Jobs\ImportProductsFromCsv;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Http\Request;

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
            'base_price' => 'required|numeric|min:0',
            'is_active'  => 'boolean',
        ]);

        $data['user_id'] = $request->user()->id;

        $product = $this->service->create($data);

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load('variants'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'sometimes|numeric|min:0',
            'is_active'  => 'sometimes|boolean',
        ]);

        $product = $this->service->update($product, $data);

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
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
}
