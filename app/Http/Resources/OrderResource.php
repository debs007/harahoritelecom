<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'order_number'     => $this->order_number,
            'status'           => $this->status,
            'status_label'     => ucwords(str_replace('_', ' ', $this->status)),
            'payment_method'   => $this->payment_method,
            'payment_status'   => $this->payment_status,
            'subtotal'         => (float) $this->subtotal,
            'discount'         => (float) $this->discount,
            'exchange_discount'=> (float) ($this->exchange_discount ?? 0),
            'shipping_charge'  => (float) $this->shipping_charge,
            'total'            => (float) $this->total,
            'created_at'       => $this->created_at->toISOString(),
            'items'            => $this->whenLoaded('items', fn() =>
                $this->items->map(fn($item) => [
                    'id'              => $item->id,
                    'product_id'      => $item->product_id,
                    'product_name'    => $item->product_name,
                    'variant_details' => $item->variant_details,
                    'price'           => (float) $item->price,
                    'quantity'        => $item->quantity,
                    'subtotal'        => (float) $item->subtotal,
                    'thumbnail'       => $item->product->thumbnail
                        ? url('storage/' . $item->product->thumbnail) : null,
                ])
            ),
            'address' => $this->whenLoaded('address', fn() => [
                'full_name'    => $this->address->full_name,
                'phone'        => $this->address->phone,
                'full_address' => $this->address->full_address,
            ]),
            'tracking_number'  => $this->tracking_number,
            'courier_name'     => $this->courier_name,
            'status_logs'      => $this->whenLoaded('statusLogs', fn() =>
                $this->statusLogs->map(fn($log) => [
                    'status'     => $log->status,
                    'comment'    => $log->comment,
                    'created_at' => $log->created_at->toISOString(),
                ])
            ),
            'exchange_request' => $this->whenLoaded('exchangeRequest', fn() =>
                $this->exchangeRequest ? [
                    'brand'           => $this->exchangeRequest->old_phone_brand,
                    'model'           => $this->exchangeRequest->old_phone_model,
                    'condition'       => $this->exchangeRequest->condition,
                    'estimated_value' => (float) $this->exchangeRequest->estimated_value,
                    'status'          => $this->exchangeRequest->status,
                ] : null
            ),
        ];
    }
}
