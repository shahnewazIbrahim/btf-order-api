<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id', 'name', 'slug', 'description', 'base_price', 'is_active', 'image_path',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
