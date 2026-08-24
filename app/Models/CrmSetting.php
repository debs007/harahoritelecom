<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CrmSetting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'type'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("crm_setting_{$key}", 300, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("crm_setting_{$key}");
    }

    public static function pointValue(): float
    {
        return (float) static::get('loyalty_point_value', 0.25);
    }

    public static function pointsPer100(): float
    {
        return (float) static::get('loyalty_points_per_100', 1);
    }
}
