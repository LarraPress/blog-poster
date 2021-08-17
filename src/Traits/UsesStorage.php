<?php

namespace LarraPress\BlogPoster\Traits;

trait UsesStorage
{
    /**
     * The name of the logging channel.
     *
     * @var string $logChannelName
     */
    protected static string $logChannelName;

    /**
     * The name of the storage disk.
     *
     * @var string $storageDiskName
     */
    protected static string $storageDiskName;

    /**
     * Init properties with storage configuration.
     *
     * @return void
    */
    protected function initStorageConnection(): void
    {
        self::$logChannelName  = config('larra-press.blog-poster.log.channel');
        self::$storageDiskName = config('larra-press.blog-poster.filesystem.disk');
    }
}
