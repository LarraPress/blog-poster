<?php

namespace LarraPress\BlogPoster\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * LarraPress\BlogPoster\Models\ScrapingJob
 *
 * @property int $id
 * @property string $name
 * @property string $source
 * @property array $config
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|ScrapingJobLog[] $logs
 * @property-read int|null $logs_count
 * @method static Builder|ScrapingJob newModelQuery()
 * @method static Builder|ScrapingJob newQuery()
 * @method static Builder|ScrapingJob query()
 * @method static Builder|ScrapingJob whereConfig($value)
 * @method static Builder|ScrapingJob whereCreatedAt($value)
 * @method static Builder|ScrapingJob whereId($value)
 * @method static Builder|ScrapingJob whereName($value)
 * @method static Builder|ScrapingJob whereSource($value)
 * @method static Builder|ScrapingJob whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string $identifier_in_list
 * @property int|null $category_id
 * @method static Builder|ScrapingJob whereCategoryId($value)
 * @method static Builder|ScrapingJob whereIdentifierInList($value)
 * @property int|null $limit
 * @property int $is_draft
 * @method static Builder|ScrapingJob whereIsDraft($value)
 * @method static Builder|ScrapingJob whereLimit($value)
 * @property int|null $daily_limit
 * @method static Builder|ScrapingJob whereDailyLimit($value)
 */
class ScrapingJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'source',
        'config',
        'category_id',
        'identifier_in_list',
        'daily_limit',
        'is_draft'
    ];

    protected $casts = [
        'config' => 'array'
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(ScrapingJobLog::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(ScrapingJobArticle::class);
    }
}
