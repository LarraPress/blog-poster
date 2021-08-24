<?php

namespace LarraPress\BlogPoster\Facades;

use Illuminate\Support\Facades\Facade;

class BlogPoster extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'blog-poster';
    }
}
