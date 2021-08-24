<?php

namespace LarraPress\BlogPoster;

use Illuminate\Support\Facades\App;
use LarraPress\BlogPoster\Console\Commands\ScrapingJobMakeCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/blog-poster.php';
    const PUBLIC_PATH = __DIR__ . '/../public';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('larra-press/blog-poster.php'),
            self::PUBLIC_PATH => public_path('vendor/larra-press/blog-poster'),
        ], 'larrapress-blog-poster');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'blog-poster');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ScrapingJobMakeCommand::class
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'larra-press.blog-poster'
        );

        App::bind('blog-poster', function()
        {
            return new BlogPoster();
        });
    }
}
