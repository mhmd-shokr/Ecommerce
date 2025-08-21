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
            "id"            => $this->id,
            "name"          => $this->name,
            "slug"          => $this->slug,
            "short_desc"    => $this->short_desc,
            "description"   => $this->description,
            "regular_price" => $this->regular_price,
            "sale_price"    => $this->sale_price,
            "SKU"           => $this->SKU,
            "stock_status"  => $this->stock_status,
            "featured"      => $this->featured,
            "quantity"      => $this->quantity,
            "image"         => asset("storage/products/thumbnails/".$this->image),
            "images"        => $this->images ? collect(explode(',',$this->images))->map(fn($img)=>asset("storage/products/thumbnails/".$this->image)):[],

            "category"      => new CategoryResource($this->whenLoaded('category')),
            "brand"         => new BrandResource($this->whenLoaded('brand')),
        ];
    }
}
