<?php
return [
    /*
    |----------------------------------------------------------------------
    | CRM Access Key
    |----------------------------------------------------------------------
    | Set CRM_ACCESS_KEY in your .env file.
    | The CRM is only accessible by pasting this key in the access form.
    | It is NEVER stored in the database or sent over a URL.
    | Generate a strong key: php artisan tinker --execute="echo Str::random(48);"
    */
    'access_key' => env('CRM_ACCESS_KEY', null),

    /*
    | Session lifetime for CRM access in minutes (default: 480 = 8 hours)
    */
    'session_lifetime' => env('CRM_SESSION_LIFETIME', 480),

    /*
    | Loyalty: points earned per ₹100 spent (default: 1 point)
    */
    'points_per_100' => env('CRM_POINTS_PER_100', 1),

    /*
    | Loyalty: ₹ value of 1 point during redemption (default: ₹0.25)
    */
    'point_value_inr' => env('CRM_POINT_VALUE', 0.25),

    /*
    | Price segment boundaries (INR) used for auto-classification
    */
    'segments' => [
        'budget'    => ['min' => 9000,   'max' => 20000,  'label' => 'Budget',     'color' => 'gray'],
        'mid_range' => ['min' => 20001,  'max' => 40000,  'label' => 'Mid-Range',  'color' => 'blue'],
        'upper_mid' => ['min' => 40001,  'max' => 70000,  'label' => 'Upper Mid',  'color' => 'indigo'],
        'premium'   => ['min' => 70001,  'max' => 100000, 'label' => 'Premium',    'color' => 'purple'],
        'flagship'  => ['min' => 100001, 'max' => 145000, 'label' => 'Flagship',   'color' => 'yellow'],
    ],
];
