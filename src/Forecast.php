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

        $predictions = $this->predictionsByCityId($cityId);
        $thePrediction = $this->findPrediction($predictions, $datetime);
        // If we have to return the wind information
        if ($wind) {
            return $thePrediction['wind_speed'];
        } else {
            return $thePrediction['weather_state_name'];
        }
    }

    private function hasPredictionAvailableFor(\DateTime $datetime = null): bool
    {
        return $datetime < new \DateTime("+6 days 00:00:00");
    }

    public function findCityId(string $city): string
    {
        $cityIdUrl = "https://www.metaweather.com/api/location/search/?query=$city";
        $response = $this->makeGetRequest($cityIdUrl);
        return json_decode($response, true)[0]['woeid'];
    }

    private function predictionsByCityId(string $cityId): array
    {
        $weatherUrl = "https://www.metaweather.com/api/location/$cityId";
        $response = $this->makeGetRequest($weatherUrl);
        return json_decode($response, true)['consolidated_weather'];
    }

    private function findPrediction(array $predictions, \DateTime $datetime): array
    {
        foreach ($predictions as $prediction) {
            if ($prediction["applicable_date"] == $datetime->format('Y-m-d')) {
                return $prediction;
            }
        }
        return [];
    }

    private function makeGetRequest(string $url): string
    {
        $client = new Client();
        return $client->get($url)->getBody()->getContents();
    }
}