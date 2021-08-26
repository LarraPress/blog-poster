<?php

namespace LarraPress\BlogPoster\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * LarraPress\BlogPoster\Models\ScrapingJobLog
 *
 * @property int $id
 * @property int $scraping_job_id
 * @property int $status
 * @property string $source_url
 * @property string|null $log
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ScrapingJob $job
 * @method static Builder|ScrapingJobLog newModelQuery()
 * @method static Builder|ScrapingJobLog newQuery()
 * @method static Builder|ScrapingJobLog query()
 * @method static Builder|ScrapingJobLog whereCreatedAt($value)
 * @method static Builder|ScrapingJobLog whereId($value)
 * @method static Builder|ScrapingJobLog whereLog($value)
 * @method static Builder|ScrapingJobLog whereScrapingJobId($value)
 * @method static Builder|ScrapingJobLog whereSourceUrl($value)
 * @method static Builder|ScrapingJobLog whereStatus($value)
 * @method static Builder|ScrapingJobLog whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int $scraped_posts_count
 * @method static Builder|ScrapingJobLog whereScrapedPostsCount($value)
 */
class ScrapingJobLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'scraping_job_id',
        'status',
        'source_url',
        'log',
        'scraped_posts_count'
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(ScrapingJob::class, 'scraping_job_id', 'id');
    }
}
