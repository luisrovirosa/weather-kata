<?php

namespace Codium\CleanCode;

use GuzzleHttp\Client;

class Forecast
{
    public function predict(string $city, \DateTime $datetime = null, bool $wind = false): string
    {
        if (!$this->hasPredictionAvailableFor($datetime)) {
            return "";
        }

        // When date is not provided we look for the current prediction
        if (!$datetime) {
            $datetime = new \DateTime();
        }
        $cityId = $this->findCityId($city);

        $predictions = $this->predictionsFor($cityId);
        foreach ($predictions as $prediction) {

            // When the date is the expected
            if ($prediction["applicable_date"] == $datetime->format('Y-m-d')) {
                // If we have to return the wind information
                if ($wind) {
                    return $prediction['wind_speed'];
                } else {
                    return $prediction['weather_state_name'];
                }
            }
        }
    }

    protected function hasPredictionAvailableFor(\DateTime $datetime = null): bool
    {
        return $datetime < new \DateTime("+6 days 00:00:00");
    }

    protected function makeGetRequest(string $url): string
    {
        $client = new Client();
        return $client->get($url)->getBody()->getContents();
    }

    public function findCityId(string $city): string
    {
        $cityIdUrl = "https://www.metaweather.com/api/location/search/?query=$city";
        $response = $this->makeGetRequest($cityIdUrl);
        return json_decode($response, true)[0]['woeid'];
    }

    protected function predictionsFor(string $cityId): array
    {
        $weatherUrl = "https://www.metaweather.com/api/location/$cityId";
        $response = $this->makeGetRequest($weatherUrl);
        return json_decode($response, true)['consolidated_weather'];
    }
}