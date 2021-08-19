<?php

namespace LarraPress\BlogPoster;

use Illuminate\Support\Facades\App;
use LarraPress\BlogPoster\Console\Commands\ScrapingJobMakeCommand;
use \Illuminate\Support\ServiceProvider;

class BlogPosterServiceProvider extends ServiceProvider
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
