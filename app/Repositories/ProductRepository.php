<?php

// app/Repositories/ProductRepository.php
namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository
{
    public function search(array $filters = [])
    {
        $query = Product::query()->with('variants');

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->paginate(15);
    }

    public function paginated(array $filters = [], int $perPage = 15)
    {
        $query = Product::query()
            ->with('vendor')
            ->orderByDesc('id');

        // 🔎 search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            // MySQL fulltext থাকলে ওটা ইউজ করব
            if (DB::connection()->getDriverName() === 'mysql') {
                $query->whereFullText(['name', 'description'], $search);
            } else {
                // fallback (লোকাল অন্য ড্রাইভার হলে)
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }
        }


        if (!empty($filters['vendor_id'])) {
            $query->where('user_id', $filters['vendor_id']);
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Product
    {
        return Product::with('variants')->find($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
