<?php
namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\CrmSetting;

class LoyaltyService
{
    // Max redemption: 10% of order value
    const MAX_REDEMPTION_PERCENT = 10;

    public static function pointValue(): float
    {
        return CrmSetting::pointValue();
    }

    public static function pointsPer100(): float
    {
        return CrmSetting::pointsPer100();
    }

    public static function pointsForOrder(Order $order): int
    {
        return (int) floor($order->total * (static::pointsPer100() / 100));
    }

    public static function pointsToInr(int $points): float
    {
        return round($points * static::pointValue(), 2);
    }

    public static function awardForOrder(Order $order): void
    {
        $user = $order->user;
        if (!$user) return;
        $points = self::pointsForOrder($order);
        if ($points <= 0) return;
        $user->addLoyaltyPoints(
            $points,
            'earned',
            "Earned for order #{$order->order_number}",
            $order->id
        );
        self::syncContactFromUser($user);
    }

    public static function syncContactFromUser(User $user): void
    {
        $totalSpent  = $user->orders()->where('status','delivered')->sum('total');
        $totalOrders = $user->orders()->where('status','delivered')->count();
        $avgOrderVal = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
        $segment     = \App\Models\CrmContact::segmentFromSpend($avgOrderVal);

        $contactType = $totalOrders > 0 ? 'buyer' : 'registered';

        // Sync BOTH users.crm_segment AND crm_contacts.segment
        $user->update(['crm_segment' => $segment]);

        $contact = $user->crmContact;
        $addr    = $user->addresses()->where('is_default', true)->first();
        $data = [
            'name'         => $user->name,
            'email'        => $user->email,
            'phone'        => $user->phone,
            'city'         => $addr?->city,
            'state'        => $addr?->state,
            'pincode'      => $addr?->pincode,
            'segment'      => $segment,
            'contact_type' => $contactType,
            'total_spent'  => $totalSpent,
            'total_orders' => $totalOrders,
            'status'       => $totalOrders > 0 ? 'active' : 'prospect',
        ];
        if ($contact) {
            $contact->update($data);
        } else {
            \App\Models\CrmContact::create(array_merge($data, ['user_id' => $user->id, 'source' => 'organic']));
        }
    }

    public static function buildWhatsappLink(string $phone, string $message): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (!str_starts_with($phone, '91')) $phone = '91' . $phone;
        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }

    public static function buildSmsLink(string $phone, string $message): string
    {
        return 'sms:' . $phone . '?body=' . urlencode($message);
    }
}
