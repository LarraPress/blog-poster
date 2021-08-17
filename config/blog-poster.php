<?php

use App\Models\Post;

return [
    'model' => Post::class,
    'filesystem' => [
        'disk' => 'public'
    ],
    'log' => [
        'channel' => 'scrape'
    ]
];
