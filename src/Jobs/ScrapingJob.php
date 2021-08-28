<?php

namespace LarraPress\BlogPoster\Jobs;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use LarraPress\BlogPoster\Crawler;
use LarraPress\BlogPoster\Crawler\ArticleAttribute;
use LarraPress\BlogPoster\Enums\JobStatus;
use LarraPress\BlogPoster\Models\ScrapingJob as ScrapingJobModel;
use LarraPress\BlogPoster\Models\ScrapingJobLog;
use LarraPress\BlogPoster\Traits\UsesStorage;

abstract class ScrapingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, UsesStorage;

    /**
     * The model  which contains all configs.
     *
     * @var ScrapingJobModel
     */
    protected ScrapingJobModel $scrapingJobModel;

    /**
     * Scraping job logging model.
     *
     * @var ScrapingJobLog
     */
    protected ScrapingJobLog $scrapingJobLog;

    /**
     * Scraped posts collection.
     *
     * @var Collection
     */
    protected Collection $scrapedPosts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ScrapingJobModel $scrapingJobModel)
    {
        $this->scrapingJobModel = $scrapingJobModel;
        $this->scrapingJobLog = ScrapingJobLog::create([
            'scraping_job_id' => $this->scrapingJobModel->id,
            'source_url' => $this->scrapingJobModel->source,
            'status' => JobStatus::processing(),
        ]);

        $this->scrapedPosts = new Collection();
        $this->initStorageConnection();
    }

    /**
     * Scrape posts from the given source.
     * Create a crawler instance, set all configs.
     * Will return true if successfully scraped all posts and set to $scrapedPosts property.
     * Otherwise, false will be returned.
     *
     * @return bool
     * @throws GuzzleException
     */
    protected function scrapePosts(): bool
    {
        $crawler = new Crawler();

        $articleAttributes = [];
        $fileAttributes = new Collection();
        $dailyLimit = $this->scrapingJobModel->daily_limit;
        $postsScrapedToday = ScrapingJobLog::whereScrapingJobId($this->scrapingJobModel->id)
            ->whereDate('created_at', '=', now())
            ->select(DB::raw('SUM(scraped_posts_count) as sum'))
            ->get()
            ->pluck('sum')
            ->first();

        if (! is_null($dailyLimit)) {
            $postsToScrape = $dailyLimit - $postsScrapedToday;
        }

        try {
            foreach ($this->scrapingJobModel->config as $configItem) {
                $articleAttribute = new ArticleAttribute($configItem['name']);
                $articleAttribute->setSelector($configItem['selector'])
                    ->isFile($configItem['is_file'])
                    ->isHtml($configItem['is_html'])
                    ->asThumbnail($configItem['as_thumb']);

                if (trim($configItem['type']) !== '') {
                    $articleAttribute->setType(trim($configItem['type']));
                }

                if (trim($configItem['custom_tag']) !== '') {
                    $articleAttribute->setTagAttribute(trim($configItem['custom_tag']));
                }

                if (! empty($configItem['ignoring_attributes'])) {
                    foreach ($configItem['ignoring_attributes'] as $ignoringAttribute) {
                        $articleAttribute->setIgnoringNode($ignoringAttribute);
                    }
                }

                if (! empty($configItem['replacing_attributes'])) {
                    foreach ($configItem['replacing_attributes'] as $replacingAttribute) {
                        $articleAttribute->setReplacingAttribute(
                            $replacingAttribute['selector'],
                            $replacingAttribute['replacing_attribute'],
                            $replacingAttribute['attribute_to_get_value_from'],
                        );
                    }
                }

                if ($configItem['is_file'] === true) {
                    $fileAttributes->push($articleAttribute->getName());
                }

                $articleAttributes[] = $articleAttribute;
            }

            $crawler->setListUri($this->scrapingJobModel->source)
                ->setArticleIdentifierInListSelector($this->scrapingJobModel->identifier_in_list)
                ->setArticleAttributes($articleAttributes);

            if (! is_null($dailyLimit)) {
                $crawler->setLimit($postsToScrape);
            }

            $this->scrapedPosts = $crawler->run();

            return true;
        } catch (Exception $exception) {
            $exceptionData = [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'backtrace' => $exception->getTraceAsString(),
            ];

            $this->scrapingJobLog->status = JobStatus::failed();
            $this->scrapingJobLog->log = $exceptionData;
            $this->scrapingJobLog->save();

            foreach ($fileAttributes ?? [] as $fileAttribute) {
                foreach ($this->scrapedPosts->pluck($fileAttribute) as $filesContainer) {
                    foreach ($filesContainer as $file) {
                        if (Storage::disk($this->storageDiskName)->exists($file['path'])) {
                            Storage::disk($this->storageDiskName)->delete($file['path']);
                        }
                    }
                }
            }

            Log::channel($this->logChannelName)->info('Scraping for '.$this->scrapingJobModel->name.' failed: '.json_encode($exceptionData));

            return false;
        }
    }
}
