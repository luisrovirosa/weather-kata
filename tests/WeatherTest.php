<?php

namespace Tests\Codium\CleanCode;

use Codium\CleanCode\Forecast;
use Codium\CleanCode\MetaWeatherForecast;
use PHPUnit\Framework\TestCase;

class WeatherTest extends TestCase
{
    // https://www.metaweather.com/api/location/766273/
    /** @test */
    public function find_the_weather_of_today()
    {
        $forecast = $this->newForecast();
        $city = "Madrid";

        $prediction = $forecast->predictWeather($city);

        echo "Today: $prediction\n";
        $this->assertTrue(true, 'I don\'t know how to test it');
    }

    /** @test */
    public function find_the_weather_of_any_day()
    {
        $forecast = $this->newForecast();
        $city = "Madrid";

        $prediction = $forecast->predictWeather($city, new \DateTime('+2 days'));

        echo "Day after tomorrow: $prediction\n";
        $this->assertTrue(true, 'I don\'t know how to test it');
    }

    /** @test */
    public function find_the_wind_of_any_day()
    {
        $forecast = $this->newForecast();
        $city = "Madrid";

        $prediction = $forecast->predictWind($city, null);

        echo "Wind: $prediction\n";
        $this->assertTrue(true, 'I don\'t know how to test it');
    }

    /** @test */
    public function change_the_city_to_woeid()
    {
        /** @var MetaWeatherForecast $forecast */
        $forecast = $this->newForecast();

        $cityId = $forecast->findCityId("Madrid");

        $this->assertEquals("766273", $cityId);
    }

    /** @test */
    public function there_is_no_prediction_for_more_than_5_days()
    {
        $forecast = $this->newForecast();
        $city = "Madrid";

        $prediction = $forecast->predictWeather($city, new \DateTime('+6 days'));

        $this->assertEquals("", $prediction);
    }

    private function newForecast(): Forecast
    {
        $httpClient = CachedGuzzleHttpClient::getInstance();
        return new MetaWeatherForecast($httpClient);
    }
}