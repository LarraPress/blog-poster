<?php

namespace LarraPress\BlogPoster;

use LarraPress\BlogPoster\Console\Commands\ScrapingJobMakeCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/blog-poster.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('larra-press/blog-poster.php'),
        ], 'larrapress-blog-poster');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ScrapingJobMakeCommand::class
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'larra-press.blog-poster'
        );
    }
}
