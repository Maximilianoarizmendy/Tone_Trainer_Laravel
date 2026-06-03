<?php
return [
    'public' => env('STRIPE_PUBLIC'),
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    'mode' => env('STRIPE_MODE', 'test'),
    'currency' => env('STRIPE_CURRENCY', 'usd'),
];
?>
