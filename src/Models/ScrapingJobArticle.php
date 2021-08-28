<?php

namespace LarraPress\BlogPoster\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ScrapingJobArticle.
 *
 * @property int $id
 * @property int $scraping_job_id
 * @property string $source_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScrapingJobArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapingJobArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapingJobArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapingJobArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapingJobArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapingJobArticle whereScrapingJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapingJobArticle whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScrapingJobArticle whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read ScrapingJob $job
 */
class ScrapingJobArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'scraping_job_id',
        'source_url',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(ScrapingJob::class);
    }
}
