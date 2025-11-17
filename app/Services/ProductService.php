<?php

// app/Services/ProductService.php
namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        protected ProductRepository $products
    ) {}

    public function list(array $filters = [])
    {
        return $this->products->search($filters);
    }

    public function create(array $data): Product
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        return $this->products->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        if (!empty($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $this->products->update($product, $data);
    }

    public function delete(Product $product): void
    {
        $this->products->delete($product);
    }
}
