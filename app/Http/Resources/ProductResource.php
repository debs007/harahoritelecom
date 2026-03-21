<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'brand'             => ['id' => $this->brand->id, 'name' => $this->brand->name],
            'category'          => ['id' => $this->category->id, 'name' => $this->category->name, 'slug' => $this->category->slug],
            'short_description' => $this->short_description,
            'description'       => $this->description,
            'price'             => (float) $this->price,
            'sale_price'        => $this->sale_price ? (float) $this->sale_price : null,
            'current_price'     => (float) $this->getCurrentPrice(),
            'discount_percent'  => $this->getDiscountPercent(),
            'sku'               => $this->sku,
            'stock'             => $this->stock,
            'in_stock'          => $this->isInStock(),
            'is_featured'       => (bool) $this->is_featured,
            'colors'            => $this->colors ?? [],
            'specs' => [
                'display_size'  => $this->display_size,
                'display_type'  => $this->display_type,
                'processor'     => $this->processor,
                'ram'           => $this->ram,
                'storage'       => $this->storage,
                'battery'       => $this->battery,
                'camera_main'   => $this->camera_main,
                'camera_front'  => $this->camera_front,
                'os'            => $this->os,
                'network'       => $this->network,
            ],
            'thumbnail'      => $this->thumbnail ? url('storage/' . $this->thumbnail) : null,
            'images'         => ImageResource::collection($this->whenLoaded('images')),
            'variants'       => VariantResource::collection($this->whenLoaded('variants')),
            'avg_rating'     => round((float)($this->avg_rating ?? 0), 1),
            'review_count'   => (int)($this->review_count ?? 0),
            'exchange_offer' => $this->whenLoaded('exchangeOffer', fn() =>
                $this->exchangeOffer ? [
                    'max_value' => (float) $this->exchangeOffer->max_exchange_value,
                    'terms'     => $this->exchangeOffer->terms,
                ] : null
            ),
        ];
    }
}
