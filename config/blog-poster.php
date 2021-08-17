<?php

use App\Models\Post;

return [
    'model' => Post::class,
    'allow_duplications' => false,
    'filesystem' => [
        'disk' => 'public'
    ],
    'log' => [
        'channel' => 'scrape'
    ]
];
