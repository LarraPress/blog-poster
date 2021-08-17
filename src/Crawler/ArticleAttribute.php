<?php

namespace LarraPress\BlogPoster\Crawler;

use Illuminate\Support\Collection;

class ArticleAttribute
{
    public const TYPE_URL = 'url';
    public const TYPE_ARRAY = 'array';

    protected string $selector;
    protected ?string $tagAttribute = null;
    protected Collection $ignoringNodes;
    protected Collection $tags;
    protected Collection $replacingAttributes;
    protected ?string $type = null;
    protected string $attributeName;
    protected bool $isHtml = false;
    protected bool $isFile = false;
    protected bool $asThumbnail = false;

    public function __construct(string $attributeName)
    {
        $this->ignoringNodes = new Collection();
        $this->tags = new Collection();
        $this->replacingAttributes = new Collection();
        $this->attributeName = $attributeName;
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_ARRAY,
            self::TYPE_URL
        ];
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function setSelector(string $selector): self
    {
        $this->selector = $selector;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTagAttribute(): ?string
    {
        return $this->tagAttribute;
    }

    public function setTagAttribute(string $tagAttribute): self
    {
        $this->tagAttribute = $tagAttribute;

        return $this;
    }

    public function getIgnoringNodes(): Collection
    {
        return $this->ignoringNodes;
    }

    public function setIgnoringNode(string $plainNode): self
    {
        $this->ignoringNodes->push($plainNode);

        return $this;
    }

    public function getName(): string
    {
        return $this->attributeName;
    }

    public function isHtml(bool $setNewState = null)
    {
        if(! is_null($setNewState)) {
            $this->isHtml = $setNewState;

            return $this;
        }

        return $this->isHtml;
    }

    public function isFile(bool $setNewState = null)
    {
        if(! is_null($setNewState)) {
            $this->isFile = $setNewState;

            return $this;
        }

        return $this->isFile;
    }

    public function asThumbnail(bool $setNewState = null)
    {
        if(! is_null($setNewState)) {
            $this->asThumbnail = $setNewState;

            return $this;
        }

        return $this->asThumbnail;
    }

    public function setReplacingAttribute(string $selector, string $replacingAttribute, string $attributeToGetValueFrom): self
    {
        $this->replacingAttributes->push([
            "selector" => $selector,
            "replacing_attribute" => $replacingAttribute,
            "attribute_to_get_value_from" => $attributeToGetValueFrom,
        ]);

        return $this;
    }

    public function getReplacingAttributes(): Collection
    {
        return $this->replacingAttributes;
    }
}
