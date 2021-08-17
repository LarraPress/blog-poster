<?php

namespace LarraPress\BlogPoster\Crawler;

use Symfony\Component\DomCrawler\Crawler;

class HtmlEditor
{
    public static function changeSelectorAttributeValue(string $body,
                                                        string $selector,
                                                        string $attribute,
                                                        string $valueWithWildCard): string
    {
        $dom = new Crawler($body);

        $dom->filter($selector)->each(function (Crawler $node) use ($attribute, $valueWithWildCard){
            $attrValue = $node->attr($attribute);
            $newValueToSet = str_replace('{*}', $attrValue, $valueWithWildCard);

            $node->getNode(0)->setAttribute($attribute, $newValueToSet);
        });

        return $dom->html();
    }

    public static function replaceAttributeValueWithAnotherAttribute(string $body,
                                                        string $selector,
                                                        string $attribute,
                                                        string $attributeToGetValue): string
    {
        $dom = new Crawler($body);

        $dom->filter($selector)->each(function (Crawler $node) use ($attribute, $attributeToGetValue){
            $newValueToSet = $node->attr($attributeToGetValue);

            $node->getNode(0)->setAttribute($attribute, $newValueToSet);
        });

        return $dom->html();
    }
}
