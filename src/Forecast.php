<?php

namespace Codium\CleanCode;

interface Forecast
{
    public function predictWeather(string $cityName, \DateTime $datetime = null);

    public function predictWind(string $cityName, \DateTime $datetime = null);
}