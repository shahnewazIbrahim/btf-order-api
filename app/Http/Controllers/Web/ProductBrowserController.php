<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductBrowserController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index()
    {
        $products = Product::with('vendor')->latest()->paginate(15);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        if (! auth('web')->check()) {
            return redirect()->route('login')->with('success', 'Please login first.');
        }

        return view('products.create');
    }

    public function store(Request $request)
    {
        if (! auth('web')->check()) {
            return redirect()->route('login')->with('success', 'Please login first.');
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'base_price'  => ['required', 'numeric', 'min:0'],
            'is_active'   => ['sometimes', 'boolean'],
        ]);

        $data['user_id']   = auth('web')->id();
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $this->productService->create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    // ✅ Show (single product details – চাইলে simple, না চাইলে skip করতে পারো)
    public function show(Product $product)
    {
        $product->load('vendor');

        return view('products.show', compact('product'));
    }

    // ✅ Edit form
    public function edit(Product $product)
    {
        if (! auth('web')->check()) {
            return redirect()->route('login')->with('success', 'Please login first.');
        }

        // Optional: শুধু owner বা admin ই edit করতে পারবে
        $user = auth('web')->user();
        if ($product->user_id !== $user->id && ($user->role->name ?? null) !== 'Admin') {
            abort(403, 'You are not allowed to edit this product.');
        }

        return view('products.edit', compact('product'));
    }

    // ✅ Update
    public function update(Request $request, Product $product)
    {
        if (! auth('web')->check()) {
            return redirect()->route('login')->with('success', 'Please login first.');
        }

        $user = auth('web')->user();
        if ($product->user_id !== $user->id && ($user->role->name ?? null) !== 'Admin') {
            abort(403, 'You are not allowed to update this product.');
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'base_price'  => ['required', 'numeric', 'min:0'],
            'is_active'   => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $this->productService->update($product, $data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    // ✅ Delete
    public function destroy(Product $product)
    {
        if (! auth('web')->check()) {
            return redirect()->route('login')->with('success', 'Please login first.');
        }

        $user = auth('web')->user();
        if ($product->user_id !== $user->id && ($user->role->name ?? null) !== 'Admin') {
            abort(403, 'You are not allowed to delete this product.');
        }

        $this->productService->delete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
