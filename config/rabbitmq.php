<?php

return [
    'host' => env('RABBITMQ_HOST', 'localhost'),
    'port' => env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'admin'),
    'password' => env('RABBITMQ_PASSWORD', 'admin'),
    'vhost' => env('RABBITMQ_VHOST', '/'),
    'queue' => env('RABBITMQ_QUEUE', 'price_collection'),
    'exchange' => env('RABBITMQ_EXCHANGE', 'price_updates'),
];
