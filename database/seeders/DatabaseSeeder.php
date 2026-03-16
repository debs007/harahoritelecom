<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\ShippingZone;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ────────────────────────────────────────
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@mobileshop.com',
            'phone'    => '9999999999',
            'role'     => 'admin',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name'     => 'Test Customer',
            'email'    => 'customer@test.com',
            'phone'    => '8888888888',
            'role'     => 'customer',
            'password' => Hash::make('password'),
        ]);

        // ── Categories ───────────────────────────────────
        $smartphones = Category::create(['name' => 'Smartphones',   'slug' => 'smartphones',   'is_active' => true]);
        $tablets     = Category::create(['name' => 'Tablets',       'slug' => 'tablets',       'is_active' => true]);
        $accessories = Category::create(['name' => 'Accessories',   'slug' => 'accessories',   'is_active' => true]);

        $budget   = Category::create(['name' => 'Budget Phones', 'slug' => 'budget-phones', 'is_active' => true, 'parent_id' => $smartphones->id]);
        $midrange = Category::create(['name' => 'Mid Range',     'slug' => 'mid-range',     'is_active' => true, 'parent_id' => $smartphones->id]);
        $flagship = Category::create(['name' => 'Flagship',      'slug' => 'flagship',      'is_active' => true, 'parent_id' => $smartphones->id]);

        // ── Brands ───────────────────────────────────────
        $brandData = [
            ['Samsung', 'samsung'],
            ['Apple',   'apple'],
            ['OnePlus', 'oneplus'],
            ['Realme',  'realme'],
            ['Xiaomi',  'xiaomi'],
            ['iQOO',    'iqoo'],
            ['Motorola','motorola'],
            ['Nothing', 'nothing'],
        ];

        $brands = [];
        foreach ($brandData as [$name, $slug]) {
            $brands[$slug] = Brand::create(['name' => $name, 'slug' => $slug, 'is_active' => true]);
        }

        // ── Products ─────────────────────────────────────
        $products = [
            [
                'name'              => 'Samsung Galaxy S24 Ultra',
                'brand'             => 'samsung',
                'category_id'       => $flagship->id,
                'price'             => 134999,
                'sale_price'        => 124999,
                'sku'               => 'SAM-S24U-256',
                'stock'             => 50,
                'is_featured'       => true,
                'processor'         => 'Snapdragon 8 Gen 3',
                'ram'               => '12GB',
                'storage'           => '256GB',
                'battery'           => '5000mAh',
                'camera_main'       => '200MP',
                'camera_front'      => '12MP',
                'display_size'      => '6.8"',
                'display_type'      => 'Dynamic AMOLED 2X',
                'os'                => 'Android 14',
                'network'           => '5G',
                'colors'            => ['Titanium Black', 'Titanium Gray', 'Titanium Violet'],
                'short_description' => 'The ultimate Galaxy with AI-powered features and a 200MP camera.',
            ],
            [
                'name'              => 'iPhone 15 Pro Max',
                'brand'             => 'apple',
                'category_id'       => $flagship->id,
                'price'             => 159900,
                'sale_price'        => null,
                'sku'               => 'APP-15PM-256',
                'stock'             => 30,
                'is_featured'       => true,
                'processor'         => 'Apple A17 Pro',
                'ram'               => '8GB',
                'storage'           => '256GB',
                'battery'           => '4422mAh',
                'camera_main'       => '48MP',
                'camera_front'      => '12MP',
                'display_size'      => '6.7"',
                'display_type'      => 'Super Retina XDR OLED',
                'os'                => 'iOS 17',
                'network'           => '5G',
                'colors'            => ['Natural Titanium', 'Blue Titanium', 'White Titanium', 'Black Titanium'],
                'short_description' => 'Pro-level camera system, A17 Pro chip, titanium design.',
            ],
            [
                'name'              => 'OnePlus 12',
                'brand'             => 'oneplus',
                'category_id'       => $flagship->id,
                'price'             => 64999,
                'sale_price'        => 59999,
                'sku'               => 'OP-12-256',
                'stock'             => 45,
                'is_featured'       => true,
                'processor'         => 'Snapdragon 8 Gen 3',
                'ram'               => '12GB',
                'storage'           => '256GB',
                'battery'           => '5400mAh',
                'camera_main'       => '50MP Hasselblad',
                'camera_front'      => '32MP',
                'display_size'      => '6.82"',
                'display_type'      => 'AMOLED LTPO 4.0',
                'os'                => 'OxygenOS 14',
                'network'           => '5G',
                'colors'            => ['Flowy Emerald', 'Silky Black'],
                'short_description' => 'Flagship killer with Hasselblad cameras and 100W charging.',
            ],
            [
                'name'              => 'Nothing Phone 2a',
                'brand'             => 'nothing',
                'category_id'       => $midrange->id,
                'price'             => 23999,
                'sale_price'        => 21499,
                'sku'               => 'NTH-2A-128',
                'stock'             => 100,
                'is_featured'       => true,
                'processor'         => 'MediaTek Dimensity 7200 Pro',
                'ram'               => '8GB',
                'storage'           => '128GB',
                'battery'           => '5000mAh',
                'camera_main'       => '50MP',
                'camera_front'      => '32MP',
                'display_size'      => '6.7"',
                'display_type'      => 'AMOLED 120Hz',
                'os'                => 'Nothing OS 2.5',
                'network'           => '5G',
                'colors'            => ['Black', 'White', 'Blue'],
                'short_description' => 'Unique transparent design with Glyph interface.',
            ],
            [
                'name'              => 'Realme GT 6',
                'brand'             => 'realme',
                'category_id'       => $midrange->id,
                'price'             => 34999,
                'sale_price'        => 31999,
                'sku'               => 'RM-GT6-256',
                'stock'             => 80,
                'is_featured'       => false,
                'processor'         => 'Snapdragon 8s Gen 3',
                'ram'               => '12GB',
                'storage'           => '256GB',
                'battery'           => '5500mAh',
                'camera_main'       => '50MP',
                'camera_front'      => '32MP',
                'display_size'      => '6.78"',
                'display_type'      => 'AMOLED 120Hz',
                'os'                => 'Android 14',
                'network'           => '5G',
                'colors'            => ['Fluid Silver', 'Razor Green'],
                'short_description' => 'Flagship performance at a mid-range price.',
            ],
            [
                'name'              => 'Xiaomi Redmi Note 13 Pro',
                'brand'             => 'xiaomi',
                'category_id'       => $budget->id,
                'price'             => 26999,
                'sale_price'        => 24999,
                'sku'               => 'XI-RN13P-128',
                'stock'             => 120,
                'is_featured'       => false,
                'processor'         => 'MediaTek Dimensity 7200',
                'ram'               => '8GB',
                'storage'           => '128GB',
                'battery'           => '5100mAh',
                'camera_main'       => '200MP',
                'camera_front'      => '16MP',
                'display_size'      => '6.67"',
                'display_type'      => 'AMOLED 120Hz',
                'os'                => 'Android 13',
                'network'           => '5G',
                'colors'            => ['Midnight Black', 'Aurora Purple', 'Forest Green'],
                'short_description' => '200MP camera powerhouse at an unbeatable price.',
            ],
            [
                'name'              => 'iQOO Neo 9 Pro',
                'brand'             => 'iqoo',
                'category_id'       => $midrange->id,
                'price'             => 36999,
                'sale_price'        => 34999,
                'sku'               => 'IQ-N9P-256',
                'stock'             => 60,
                'is_featured'       => true,
                'processor'         => 'Snapdragon 8 Gen 2',
                'ram'               => '12GB',
                'storage'           => '256GB',
                'battery'           => '5160mAh',
                'camera_main'       => '50MP',
                'camera_front'      => '16MP',
                'display_size'      => '6.78"',
                'display_type'      => 'AMOLED 144Hz',
                'os'                => 'Android 14',
                'network'           => '5G',
                'colors'            => ['Fiery Red', 'Loyal Black'],
                'short_description' => 'Gaming beast with 144Hz display and 120W fast charging.',
            ],
            [
                'name'              => 'Motorola Edge 50 Pro',
                'brand'             => 'motorola',
                'category_id'       => $midrange->id,
                'price'             => 31999,
                'sale_price'        => 29999,
                'sku'               => 'MOT-E50P-256',
                'stock'             => 70,
                'is_featured'       => false,
                'processor'         => 'Snapdragon 7s Gen 2',
                'ram'               => '12GB',
                'storage'           => '256GB',
                'battery'           => '4500mAh',
                'camera_main'       => '50MP',
                'camera_front'      => '50MP',
                'display_size'      => '6.7"',
                'display_type'      => 'pOLED 144Hz',
                'os'                => 'Android 14',
                'network'           => '5G',
                'colors'            => ['Luxe Lavender', 'Black Beauty'],
                'short_description' => 'Sleek design with 125W TurboPower charging.',
            ],
        ];

        foreach ($products as $p) {
            \App\Models\Product::create([
                'name'              => $p['name'],
                'slug'              => Str::slug($p['name']),
                'category_id'       => $p['category_id'],
                'brand_id'          => $brands[$p['brand']]->id,
                'price'             => $p['price'],
                'sale_price'        => $p['sale_price'],
                'sku'               => $p['sku'],
                'stock'             => $p['stock'],
                'track_stock'       => true,
                'processor'         => $p['processor'],
                'ram'               => $p['ram'],
                'storage'           => $p['storage'],
                'battery'           => $p['battery'],
                'camera_main'       => $p['camera_main'],
                'camera_front'      => $p['camera_front'],
                'display_size'      => $p['display_size'],
                'display_type'      => $p['display_type'],
                'os'                => $p['os'],
                'network'           => $p['network'],
                'colors'            => $p['colors'],
                'is_featured'       => $p['is_featured'],
                'is_active'         => true,
                'short_description' => $p['short_description'],
                'description'       => $p['short_description']
                    . "\n\nKey Features:\n"
                    . "• Display: {$p['display_size']} {$p['display_type']}\n"
                    . "• Processor: {$p['processor']}\n"
                    . "• RAM: {$p['ram']} | Storage: {$p['storage']}\n"
                    . "• Camera: {$p['camera_main']} main + {$p['camera_front']} front\n"
                    . "• Battery: {$p['battery']}\n"
                    . "• OS: {$p['os']} | Network: {$p['network']}",
            ]);
        }

        // ── Shipping Zones ───────────────────────────────
        ShippingZone::create([
            'name'           => 'Metro Cities',
            'states'         => ['Maharashtra', 'Delhi', 'Karnataka', 'Tamil Nadu', 'Telangana', 'West Bengal'],
            'rate'           => 49,
            'free_above'     => 999,
            'estimated_days' => 2,
            'is_active'      => true,
        ]);
        ShippingZone::create([
            'name'           => 'Tier 2 Cities',
            'states'         => ['Gujarat', 'Rajasthan', 'Madhya Pradesh', 'Uttar Pradesh', 'Punjab', 'Haryana'],
            'rate'           => 79,
            'free_above'     => 1499,
            'estimated_days' => 4,
            'is_active'      => true,
        ]);
        ShippingZone::create([
            'name'           => 'Rest of India',
            'states'         => ['Other'],
            'rate'           => 99,
            'free_above'     => 1999,
            'estimated_days' => 7,
            'is_active'      => true,
        ]);

        // ── Coupons ──────────────────────────────────────
        Coupon::create([
            'code'             => 'SAVE10',
            'description'      => '10% off on first order',
            'type'             => 'percent',
            'value'            => 10,
            'min_order_amount' => 5000,
            'max_discount'     => 2000,
            'usage_limit'      => 1000,
            'is_active'        => true,
            'expires_at'       => now()->addYear(),
        ]);
        Coupon::create([
            'code'             => 'FLAT500',
            'description'      => '₹500 flat off on orders above ₹25,000',
            'type'             => 'fixed',
            'value'            => 500,
            'min_order_amount' => 25000,
            'usage_limit'      => 500,
            'is_active'        => true,
            'expires_at'       => now()->addMonths(6),
        ]);
        Coupon::create([
            'code'             => 'SUMMER20',
            'description'      => '20% off — Summer Sale',
            'type'             => 'percent',
            'value'            => 20,
            'min_order_amount' => 10000,
            'max_discount'     => 3000,
            'is_active'        => true,
            'expires_at'       => now()->addMonths(2),
        ]);
    }
}
