<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = ucwords(str_replace('_', ' ', $this->order->status));

        $message = (new MailMessage)
            ->subject("📦 Order Update — {$statusLabel} | #{$this->order->order_number}")
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line("Your order **#{$this->order->order_number}** has been updated to **{$statusLabel}**.");

        if ($this->order->tracking_number) {
            $message->line("🚚 **Courier:** {$this->order->courier_name}")
                    ->line("📋 **Tracking Number:** {$this->order->tracking_number}");
        }

        if ($this->order->status === 'delivered') {
            $message->line('We hope you enjoy your new phone! Please leave a review.');
        }

        return $message
            ->action('View Order Details', url('/orders/' . $this->order->id))
            ->line('Thank you for shopping with MobileShop!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'order_status_updated',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'status'       => $this->order->status,
        ];
    }
}
