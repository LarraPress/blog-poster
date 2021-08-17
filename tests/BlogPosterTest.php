<?php

namespace LarraPress\BlogPoster\Tests;

use LarraPress\BlogPoster\ServiceProvider;
use Orchestra\Testbench\TestCase;

class BlogPosterTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'blog-poster' => BlogPoster::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
