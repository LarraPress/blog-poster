<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\PostCategory;

return [
    // The model which presents the articles
    'model' => Post::class,

    // The model which presents posts categories
    // Set to null if you don't have categories
    'category' => Category::class,

    'category_post_relation' => PostCategory::class,

    // Leave it false if you don't want to see duplicated posts
    'allow_duplications' => false,

    // The disk name to store scraped media
    'filesystem' => [
        'disk' => 'public'
    ],

    // The log channel name to use when logging
    'log' => [
        'channel' => 'scrape'
    ]
];
