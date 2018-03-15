<?php

namespace Codium\CleanCode;

use GuzzleHttp\Client;

class HttpClient
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