<?php

namespace Codium\CleanCode;

interface HttpClient
{
    public function get(string $url): string;
}