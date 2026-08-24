<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'email', 'phone', 'avatar', 'role', 'password',
        'is_active', 'loyalty_points', 'city', 'state', 'pincode', 'crm_segment',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
        'loyalty_points'    => 'integer',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function defaultAddress()
    {
        return $this->addresses()->where('is_default', true)->first();
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function crmContact()
    {
        return $this->hasOne(CrmContact::class);
    }

    public function addLoyaltyPoints(int $points, string $type, string $description, ?int $orderId = null): void
    {
        $newBalance = max(0, $this->loyalty_points + $points);
        $this->update(['loyalty_points' => $newBalance]);
        LoyaltyTransaction::create([
            'user_id'       => $this->id,
            'order_id'      => $orderId,
            'type'          => $type,
            'points'        => $points,
            'balance_after' => $newBalance,
            'description'   => $description,
        ]);
    }
}