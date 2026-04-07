<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'ram'              => $this->ram,
            'storage'          => $this->storage,
            'price'            => (float) $this->price,
            'sale_price'       => $this->sale_price ? (float) $this->sale_price : null,
            'stock'            => $this->stock,
            'in_stock'         => $this->stock > 0,
            'sku'              => $this->sku,
            'available_colors' => $this->available_colors ?? [],
            'label'            => $this->getDetailsLabel(),
        ];
    }
}
