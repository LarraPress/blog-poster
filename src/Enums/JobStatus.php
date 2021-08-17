<?php


namespace LarraPress\BlogPoster\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self processing()
 * @method static self processed()
 * @method static self failed()
*/
final class JobStatus extends Enum
{
    /**
     * @return array
     */
    protected static function values(): array
    {
        return [
            'processing' => 1,
            'processed' => 2,
            'failed' => 3,
        ];
    }
}
