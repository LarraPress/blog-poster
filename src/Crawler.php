<?php

namespace LarraPress\BlogPoster;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use LarraPress\BlogPoster\Crawler\ArticleAttribute;
use LarraPress\BlogPoster\Crawler\HtmlEditor;
use LarraPress\BlogPoster\Models\ScrapingJobArticle;
use LarraPress\BlogPoster\Traits\UsesStorage;
use Symfony\Component\DomCrawler\Crawler as SymfonyCrawler;

class Crawler
{
    use UsesStorage;

    /**
     * The URI of the articles list.
     *
     * @var string
     */
    protected string $listUri;

    /**
     * The CSS selector of the article link in the list.
     *
     * @var string
     */
    protected string $articleSelectorInList;

    /**
     * Base URI of the source.
     *
     * @var string
     */
    protected string $baseUrl;

    /**
     * The collection set of the scrapped articles.
     * If it's a testing mode, one article will be here.
     *
     * @var Collection
     */
    protected Collection $results;

    /**
     * Array of the article attributes to scrape.
     * Such as title, image, etc.
     *
     * @var array
     */
    protected array $articleAttributes;

    /**
     * Flag to determine if it's a testing mode.
     * If it is, one article will be returned from a crawler and the files will not be downloaded to the storage,
     * but the original URLs will be.
     *
     * @var bool
     */
    protected bool $testingMode;

    /**
     * The limit of the articles to scrape.
     * If it's null, all articles in the list will be scraped.
     *
     * @var int|null
     */
    protected ?int $limitOfPostsToScrape = null;

    /**
     * Guzzle Http client to send a request to the lists and single articles.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Class construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->results = new Collection();
        $this->testingMode = false;
        $this->baseUrl = '';
        $this->client = new Client();

        $this->initStorageConnection();
    }

    /**
     * Set the limit to the crawler.
     *
     * @param int $numberOfPostsToScrape
     * @return Crawler
     */
    public function setLimit(int $numberOfPostsToScrape): self
    {
        $this->limitOfPostsToScrape = $numberOfPostsToScrape;

        return $this;
    }

    /**
     * Enable the testing mode to the current process.
     *
     * @return Crawler
     */
    public function enableTestingMode(): self
    {
        $this->testingMode = true;

        return $this;
    }

    /**
     * Disable the testing mode to the current process.
     *
     * @return Crawler
     */
    public function disableTestingMode(): self
    {
        $this->testingMode = false;

        return $this;
    }

    /**
     * Set the list where the articles are.
     *
     * @param string $uri
     * @return Crawler
     */
    public function setListUri(string $uri): self
    {
        $this->listUri = $uri;

        $parsedUrl = parse_url($uri);
        $this->baseUrl = $parsedUrl['scheme'].'://'.$parsedUrl['host'];

        return $this;
    }

    /**
     * Set the CSS selector of the single article link in the list of the articles.
     *
     * @param string $selector
     * @return Crawler
     */
    public function setArticleIdentifierInListSelector(string $selector): self
    {
        $this->articleSelectorInList = $selector;

        return $this;
    }

    /**
     * The array of the ArticleAttribute object, which contain the selectors
     * and all necessary configs for scraping the article for the selected source.
     *
     * @param ArticleAttribute[] $articleAttributes
     * @return Crawler
     */
    public function setArticleAttributes(array $articleAttributes): self
    {
        $this->articleAttributes = $articleAttributes;

        return $this;
    }

    /**
     * Run crawler, use article attribute configs for scraping.
     * Scrape articles and push to the $results collection.
     *
     * @return Collection|array
     * @throws GuzzleException
     */
    public function run()
    {
        $web = new SymfonyCrawler($this->client->get($this->listUri)->getBody()->getContents());

        $iterator = $web->filter($this->articleSelectorInList);

        if ($this->testingMode) {
            $iterator = $iterator->first();
        }

        $iterator->each(function ($node) {
            if (! is_null($this->limitOfPostsToScrape) && $this->limitOfPostsToScrape <= 0) {
                return false;
            }

            $articleLink = $this->getRealUrl($node->attr('href'));

            if (! $this->testingMode
                && config('larra-press.blog-poster.allow_duplications') !== true
                && ScrapingJobArticle::whereSourceUrl($articleLink)->exists()) {
                return null;
            }

            $article = $this->crawlUri($articleLink);

            $article['source'] = $articleLink;
            $article['source_base'] = $this->baseUrl;

            $this->results->push($article);

            if (! is_null($this->limitOfPostsToScrape) && $this->limitOfPostsToScrape > 0) {
                $this->limitOfPostsToScrape--;
            }
        });

        return $this->testingMode ? $this->results->first() : $this->results;
    }

    /**
     * Process with the single article.
     * Loop over article attribute to get needed data.
     *
     * @throws GuzzleException
     * @param string $articleUri
     * @return array
     */
    public function crawlUri(string $articleUri): array
    {
        $article = [];
        $web = new SymfonyCrawler($this->client->get($articleUri)->getBody()->getContents());

        /** @var ArticleAttribute $articleAttribute */
        foreach ($this->articleAttributes as $articleAttribute) {
            if ($articleAttribute->getType() === ArticleAttribute::TYPE_ARRAY) {
                $web->filter($articleAttribute->getSelector())->each(function ($node) use ($articleAttribute, &$article, &$web) {
                    $article[$articleAttribute->getName()][] = $this->processWithAttribute($web, $articleAttribute, $node);
                });
            } else {
                $element = $web->filter($articleAttribute->getSelector())->first();

                $article[$articleAttribute->getName()] = $this->processWithAttribute($web, $articleAttribute, $element);
            }
        }

        return $article;
    }

    /**
     * Process with a single attribute of the article.
     * Determine its type and continue with its specifications.
     *
     * @param SymfonyCrawler $web
     * @param ArticleAttribute $articleAttribute
     * @param SymfonyCrawler $element
     * @return array|string|null
     */
    protected function processWithAttribute(SymfonyCrawler &$web,
                                            ArticleAttribute $articleAttribute,
                                            SymfonyCrawler $element)
    {
        if (! is_null($articleAttribute->getTagAttribute())
            && $articleAttribute->getType() === ArticleAttribute::TYPE_URL) {
            $attributeValue = $this->getRealUrl($element->attr($articleAttribute->getTagAttribute()));

            if ($articleAttribute->isFile()) {
                $attributeValue = $this->uploadFileAndReplaceLink($attributeValue, $articleAttribute->asThumbnail());
            }
        } else {
            $attributeValue = trim($element->html());
        }

        if ($articleAttribute->getIgnoringNodes()->isNotEmpty()) {
            $articleAttribute->getIgnoringNodes()->each(function (string $node) use (&$attributeValue , &$web) {
                $attributeValue = $this->removeHtmlTags($web, $attributeValue, $node);
            });
        }

        if ($articleAttribute->getReplacingAttributes()->isNotEmpty()) {
            $articleAttribute->getReplacingAttributes()->each(function (array $replacingAttributeArray) use (&$attributeValue) {
                $attributeValue = HtmlEditor::replaceAttributeValueWithAnotherAttribute(
                    $attributeValue,
                    $replacingAttributeArray['selector'],
                    $replacingAttributeArray['replacing_attribute'],
                    $replacingAttributeArray['attribute_to_get_value_from'],
                );
            });
        }

        if ($articleAttribute->isHtml()) {
            $attributeValue = $this->removeHtmlComments($attributeValue);
        }

        return $attributeValue;
    }

    /**
     * Remove comments from HTML content.
     *
     * @param string $html
     * @return string
     */
    protected function removeHtmlComments(string $html): string
    {
        return preg_replace("~<!--(?!<!)[^\[>].*?-->~s", '', $html);
    }

    /**
     * In some article there can be not full URLs, like /path/to/article.
     * This method will return a full url.
     *
     * @param string $url
     * @return string
     */
    protected function getRealUrl(string $url): string
    {
        return (! filter_var($url, FILTER_VALIDATE_URL)) ? $this->baseUrl.$url : $url;
    }

    /**
     * Remove HTML tags by selector from the HTML content.
     * This is useful for example when you don't need to include advertisement tag from the source into your post.
     *
     * @param SymfonyCrawler $web
     * @param string $html
     * @param string $selector
     * @return string
     */
    protected function removeHtmlTags(SymfonyCrawler $web, string $html, string $selector): string
    {
        $web->filter($selector)->each(function (SymfonyCrawler $node) use (&$html) {
            $html = str_replace($node->outerHtml(), '', $html);
        });

        return $html;
    }

    /**
     * If there are files to be included into article, they'll be downloaded.
     * In that case the array of the url and path will be returned.
     *
     * If this process is in the testing mode, the original URL will be returned
     * only and the file will not be downloaded.
     *
     * @param string $url
     * @param bool $asThumbnail
     * @return array|string|null
     */
    protected function uploadFileAndReplaceLink(string $url, bool $asThumbnail)
    {
        if (! $this->remoteFileExists($url)) {
            return null;
        }

        if ($this->testingMode) {
            return $url;
        }

        $contents = file_get_contents($url);
        $name = 'posts/'.now()->format('Y/m/d/').Str::random(64);

        if ($asThumbnail) {
            $img = Image::make($contents)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            if (Storage::disk($this->storageDiskName)->put($name, $img->response()->content())) {
                return [
                    'url' => Storage::disk($this->storageDiskName)->url($name),
                    'path' => $name,
                ];
            }
        } elseif (Storage::disk($this->storageDiskName)->put($name, $contents)) {
            return [
                'url' => Storage::disk($this->storageDiskName)->url($name),
                'path' => $name,
            ];
        }

        return null;
    }

    /**
     * Check if remote file exists.
     *
     * @param string $url
     * @return bool
     */
    protected function remoteFileExists(string $url): bool
    {
        try {
            return Str::endsWith(Arr::get(get_headers($url), 0), '200 OK');
        } catch (Exception $exception) {
            return false;
        }
    }
}
