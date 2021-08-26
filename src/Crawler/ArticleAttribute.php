<?php

namespace LarraPress\BlogPoster\Crawler;

use Illuminate\Support\Collection;

/**
 * There are some attributes in the articles to scrape, such as title, body, tags and so on.
 * Each element is called here ArticleAttribute.
*/
class ArticleAttribute
{
    public const TYPE_URL = 'url';
    public const TYPE_ARRAY = 'array';

    /**
     * Attribute CSS selector.
     *
     * @var string
    */
    protected string $selector;

    /**
     * Set HTML tag attribute to get data from.
     * Useful for <meta>
     * Example: content - get data from content tag attribute of some meta tag.
     *
     * @var null|string
    */
    protected ?string $tagAttribute = null;

    /**
     * The container of ignored HTML elements selectors.
     * Useful if there are ads sections in the article body.
     *
     * @var Collection
    */
    protected Collection $ignoringNodes;

    /**
     * Set data to this if you want to change the HTML tag attribute data.
     * This is useful if you scrape from modern blog which use lazy loading for media.
     * For example, the article body can have <img> tag which contains SRC and DATA-SRC attributes.
     * Since this is a lazy loading, the SRC can contain a default image, but the real image address can be in DATA-SRC.
     *
     * @var Collection $replacingAttributes
    */
    protected Collection $replacingAttributes;

    /**
     * The type of the article attribute.
     * Can be array, URL or default.
     * Array useful for tags - many items in the element.
     * URL should be set to file. The crawler will check if this is a URL - process specific manipulations, like make non-full URL to full.
     *
     * @see \LarraPress\BlogPoster\Crawler::processWithAttribute
     * @var null|string
    */
    protected ?string $type = null;

    /**
     * The name of the article attribute.
     * This name is set from dashboard and used to post the articles to DB.
     * For the first beta version the names of the article attributes and model fields are hardcoded.
     * For the coming versions there will be a mapper, which will map your article attribute to the model field.
     *
     * @todo Create mapper to use this property in the mapper, but not in the poster job.
     * @var string
    */
    protected string $attributeName;

    /**
     * The flag which determines if the attribute is an HTML.
     * Should be set to the HTML article attributes, such as article body.
     * The crawler will use this flag to do specific manipulations.
     *
     * @see \LarraPress\BlogPoster\Crawler::removeHtmlComments
     * @var bool
    */
    protected bool $isHtml = false;

    /**
     * The flag which determines if the attribute is a file.
     * If yes, it will be downloaded to the storage.
     *
     * @var bool
    */
    protected bool $isFile = false;

    /**
     * The flag which determines if the file(image) should have a thumbnail.
     * If the source provides a big feature image file, it can be scraped also as a thumbnail.
     *
     * @var bool
    */
    protected bool $asThumbnail = false;

    /**
     * @param string $attributeName
     * @return void
    */
    public function __construct(string $attributeName)
    {
        $this->ignoringNodes = new Collection();
        $this->replacingAttributes = new Collection();
        $this->attributeName = $attributeName;
    }

    /**
     * @return array
    */
    public static function getTypes(): array
    {
        return [
            self::TYPE_ARRAY,
            self::TYPE_URL
        ];
    }

    /**
     * @return string
    */
    public function getSelector(): string
    {
        return $this->selector;
    }

    /**
     * @param string $selector
     * @return ArticleAttribute
    */
    public function setSelector(string $selector): self
    {
        $this->selector = $selector;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ArticleAttribute
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getTagAttribute(): ?string
    {
        return $this->tagAttribute;
    }

    /**
     * @param string $tagAttribute
     * @return ArticleAttribute
     */
    public function setTagAttribute(string $tagAttribute): self
    {
        $this->tagAttribute = $tagAttribute;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getIgnoringNodes(): Collection
    {
        return $this->ignoringNodes;
    }

    /**
     * @param string $plainNode
     * @return ArticleAttribute
     */
    public function setIgnoringNode(string $plainNode): self
    {
        $this->ignoringNodes->push($plainNode);

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->attributeName;
    }

    /**
     * Get or set a new value for ArticleAttribute::isHtml property.
     *
     * @param bool $setNewState
     * @return ArticleAttribute|bool
    */
    public function isHtml(bool $setNewState = null)
    {
        if(! is_null($setNewState)) {
            $this->isHtml = $setNewState;

            return $this;
        }

        return $this->isHtml;
    }

    /**
     * Get or set a new value for ArticleAttribute::isFile property.
     *
     * @param bool $setNewState
     * @return ArticleAttribute|bool
     */
    public function isFile(bool $setNewState = null)
    {
        if(! is_null($setNewState)) {
            $this->isFile = $setNewState;

            return $this;
        }

        return $this->isFile;
    }

    /**
     * Get or set a new value for ArticleAttribute::asThumbnail property.
     *
     * @param bool $setNewState
     * @return ArticleAttribute|bool
     */
    public function asThumbnail(bool $setNewState = null)
    {
        if(! is_null($setNewState)) {
            $this->asThumbnail = $setNewState;

            return $this;
        }

        return $this->asThumbnail;
    }

    /**
     * Set replacing attribute for article attribute.
     *
     * @see ArticleAttribute::$replacingAttributes for more information
     * @param string $selector
     * @param string $replacingAttribute
     * @param string $attributeToGetValueFrom
     * @return ArticleAttribute
    */
    public function setReplacingAttribute(string $selector, string $replacingAttribute, string $attributeToGetValueFrom): self
    {
        $this->replacingAttributes->push([
            "selector" => $selector,
            "replacing_attribute" => $replacingAttribute,
            "attribute_to_get_value_from" => $attributeToGetValueFrom,
        ]);

        return $this;
    }

    /**
     * @see ArticleAttribute::$replacingAttributes for more information
     * @return Collection
    */
    public function getReplacingAttributes(): Collection
    {
        return $this->replacingAttributes;
    }
}
