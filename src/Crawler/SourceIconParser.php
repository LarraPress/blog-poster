<?php

namespace LarraPress\BlogPoster\Crawler;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler as SymfonyCrawler;

class SourceIconParser
{
    /**
     * Parse the icon of the selected source.
     * If the selector and attribute are not set, the default ones will be used.
     *
     * @param string $source_url
     * @param string|null $selector
     * @param string|null $attribute
     * @return false|string
     * @throws GuzzleException
     */
    public static function parseIconUrl(string $source_url, string $selector = null, string $attribute = null)
    {
        if (is_null($selector)) {
            $selector = 'link[rel*="icon"]';
            $attribute = 'href';
        }

        try {
            $client = new Client();
            $web = new SymfonyCrawler($client->get($source_url)->getBody()->getContents());
            $url = $web->filter($selector)->first()->attr($attribute);

            if ((! filter_var($url, FILTER_VALIDATE_URL))) {
                $parsedUrl = parse_url($source_url);
                $baseUrl = $parsedUrl['scheme'].'://'.$parsedUrl['host'];

                $url = $baseUrl.$url;
            }

            $fileExists = Str::endsWith(Arr::get(@get_headers($url), 0), '200 OK');

            return $fileExists ? $url : false;
        } catch (Exception $exception) {
            return false;
        }
    }
}
