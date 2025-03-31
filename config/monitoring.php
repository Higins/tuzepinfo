<?php

return [
    'rabbitmq' => [
        'enabled' => env('RABBITMQ_MONITORING_ENABLED', true),
        'host' => env('RABBITMQ_HOST', 'rabbitmq'),
        'port' => env('RABBITMQ_MANAGEMENT_PORT', 15672),
        'user' => env('RABBITMQ_USER', 'admin'),
        'password' => env('RABBITMQ_PASSWORD', 'admin'),
        'vhost' => env('RABBITMQ_VHOST', '/'),
        'metrics' => [
            'queue_length',
            'message_rate',
            'consumer_count',
            'memory_usage',
            'disk_space_usage',
        ],
    ],
];
