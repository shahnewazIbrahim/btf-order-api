<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\VariantService;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function __construct(
        protected VariantService $variants
    ) {
        // চাইলে role middleware লাগাতে পারো
        // $this->middleware('role:Admin,Vendor')->except('index', 'show');
    }

    public function index(Product $product)
    {
        $variants = $this->variants->listForProduct($product);

        return response()->json($variants);
    }

    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => ['nullable', 'string', 'max:255'],
            'sku'         => ['nullable', 'string', 'max:255', 'unique:product_variants,sku'],
            'attributes'  => ['nullable', 'array'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'stock'       => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
        ]);

        $variant = $this->variants->create($product, $data);

        return response()->json($variant, 201);
    }

    public function show(Product $product, ProductVariant $variant)
    {
        // route model binding এর জন্য ensure করি যে এই variant ঐ product এরই
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        return response()->json($variant->load('inventory'));
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $data = $request->validate([
            'name'        => ['nullable', 'string', 'max:255'],
            'sku'         => ['nullable', 'string', 'max:255', 'unique:product_variants,sku,'.$variant->id],
            'attributes'  => ['nullable', 'array'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'stock'       => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
        ]);

        $variant = $this->variants->update($variant, $data);

        return response()->json($variant);
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $this->variants->delete($variant);

        return response()->json(null, 204);
    }
}
