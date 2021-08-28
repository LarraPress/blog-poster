<?php

namespace LarraPress\BlogPoster\Traits;

trait UsesStorage
{
    /**
     * The name of the logging channel.
     *
     * @var string
     */
    protected string $logChannelName = '';

    /**
     * The name of the storage disk.
     *
     * @var string
     */
    protected string $storageDiskName = '';

    /**
     * Init properties with storage configuration.
     *
     * @return void
     */
    protected function initStorageConnection(): void
    {
        $this->logChannelName = config('larra-press.blog-poster.log.channel');
        $this->storageDiskName = config('larra-press.blog-poster.filesystem.disk');
    }
}
