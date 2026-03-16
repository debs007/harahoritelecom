<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🛍️ Order Confirmed — #' . $this->order->order_number)
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Thank you for your order. We have received it and will process it shortly.')
            ->line('**Order Number:** ' . $this->order->order_number)
            ->line('**Total:** ₹' . number_format($this->order->total, 2))
            ->line('**Payment:** ' . ucfirst($this->order->payment_method))
            ->action('Track Your Order', url('/orders/' . $this->order->id . '/track'))
            ->line('Thank you for shopping with MobileShop!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'order_placed',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'total'        => $this->order->total,
        ];
    }
}
