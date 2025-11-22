<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'base_price'  => $this->base_price,
            'is_active'   => $this->is_active,
            'image_path'  => $this->image_path,
            'image_url'   => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'variants'    => $this->whenLoaded('variants'),
            'created_at'  => $this->created_at,
        ];
    }
}
