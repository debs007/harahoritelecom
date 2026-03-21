<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'url'   => url('storage/' . $this->image),
            'color' => $this->color,
            'alt'   => $this->alt,
        ];
    }
}
