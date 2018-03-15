<?php

namespace Codium\CleanCode;

use GuzzleHttp\Client;

class HttpClient
{
    public function get(string $url)
    {
        $client = new Client();
        return $client->get($url)->getBody()->getContents();
    }
}