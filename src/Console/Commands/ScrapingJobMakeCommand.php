<?php

namespace LarraPress\BlogPoster\Console\Commands;

use Illuminate\Foundation\Console\JobMakeCommand;

class ScrapingJobMakeCommand extends JobMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:scraping_job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new scraping job class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs/scraping_job.stub';
    }
}
