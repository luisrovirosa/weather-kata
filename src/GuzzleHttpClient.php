<?php

namespace Codium\CleanCode;

use GuzzleHttp\Client;

class GuzzleHttpClient implements HttpClient
{
    /** @var Client */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function get(string $url): string
    {
        return $this->client->get($url)->getBody()->getContents();
    }
}