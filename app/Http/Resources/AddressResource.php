<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'full_name'    => $this->full_name,
            'phone'        => $this->phone,
            'address_line1'=> $this->address_line1,
            'address_line2'=> $this->address_line2,
            'city'         => $this->city,
            'state'        => $this->state,
            'pincode'      => $this->pincode,
            'country'      => $this->country,
            'is_default'   => (bool) $this->is_default,
            'full_address' => $this->full_address,
        ];
    }
}
