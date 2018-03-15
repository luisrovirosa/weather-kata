<?php

namespace Tests\Codium\CleanCode;

use Codium\CleanCode\GuzzleHttpClient;
use Codium\CleanCode\HttpClient;

class CachedGuzzleHttpClient implements HttpClient
{
    private static $instance;
    /**
     * @var HttpClient
     */
    private $client;
    /** @var array */
    private $cache;

    private function __construct(HttpClient $client)
    {
        $this->client = $client;
        $this->cache = [];
    }


    public static function getInstance(): CachedGuzzleHttpClient
    {
        if (!self::$instance){
            self::$instance = new CachedGuzzleHttpClient(new GuzzleHttpClient());
        }
        return self::$instance;
    }

    public function get(string $url): string
    {
        if (!isset($this->cache[$url])){
            $this->cache[$url] = $this->client->get($url);
        }
        return $this->cache[$url];
    }
}