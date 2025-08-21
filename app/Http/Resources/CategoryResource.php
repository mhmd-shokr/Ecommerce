<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            "name"=>$this->name,
            "slug"=>$this->slug,
            "image"=>asset('uploads/categories/' . $this->image),
            "product_count"=>$this->products_count,
            "product"=> ProductResource::collection( $this->whenLoaded('products')),

            
        ];
    }
}
