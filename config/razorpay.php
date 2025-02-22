<?php 

return [
    'webhooks' => [
        'secret' => env('RAZORPAY_WEBHOOK_SECRET'),
        'valid_status' => env('RAZORPAY_VALID_STATUS', 'authorized')
    ],
    'key' => env('RAZORPAY_KEY'),
    'secret' => env('RAZORPAY_SECRET'),
];