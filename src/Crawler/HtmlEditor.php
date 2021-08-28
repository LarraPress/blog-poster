<?php

namespace LarraPress\BlogPoster\Crawler;

use Symfony\Component\DomCrawler\Crawler;

class HtmlEditor
{
    /**
     * @todo finish this method functionality and PHPDOC, NOT USED
     */
    public static function changeSelectorAttributeValue(
        string $body,
        string $selector,
        string $attribute,
        string $valueWithWildCard): string
    {
        $dom = new Crawler($body);

        $dom->filter($selector)->each(function (Crawler $node) use ($attribute, $valueWithWildCard) {
            $attrValue = $node->attr($attribute);
            $newValueToSet = str_replace('{*}', $attrValue, $valueWithWildCard);

            $node->getNode(0)->setAttribute($attribute, $newValueToSet);
        });

        return $dom->html();
    }

    /**
     * Replace HTML tag attribute value with another one.
     *
     * @see ArticleAttribute::$replacingAttributes for more information
     * @param string $body
     * @param string $selector
     * @param string $attribute
     * @param string $attributeToGetValue
     * @return string
     */
    public static function replaceAttributeValueWithAnotherAttribute(
        string $body,
        string $selector,
        string $attribute,
        string $attributeToGetValue): string
    {
        $dom = new Crawler($body);

        $dom->filter($selector)->each(function (Crawler $node) use ($attribute, $attributeToGetValue) {
            $newValueToSet = $node->attr($attributeToGetValue);

            $node->getNode(0)->setAttribute($attribute, $newValueToSet);
        });

        return $dom->html();
    }
}
