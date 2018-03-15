<?php

namespace Codium\CleanCode;

use GuzzleHttp\Client;

class Forecast
{
    public function predict(string $cityName, \DateTime $datetime = null, bool $wind = false): string
    {
        // If we have to return the wind information
        if ($wind) {
            $thePrediction = $this->predictionsByNameOnDate($cityName, $datetime);
            return $thePrediction['wind_speed'];
        } else {
            $thePrediction = $this->predictionsByNameOnDate($cityName, $datetime);
            return $thePrediction['weather_state_name'];
        }
    }

    protected function predictionsByNameOnDate(string $cityName, \DateTime $datetime = null): array
    {
        // When date is not provided we look for the current prediction
        if (!$datetime) {
            $datetime = new \DateTime();
        }
        $predictions = $this->predictionsByCityName($cityName);
        $thePrediction = $this->findPrediction($predictions, $datetime);
        return $thePrediction;
    }

    private function predictionsByCityName(string $city): array
    {
        $cityId = $this->findCityId($city);
        return $this->predictionsByCityId($cityId);
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
        return [
            'weather_state_name' => '',
            'wind_speed'         => '',
        ];
    }

    private function makeGetRequest(string $url): string
    {
        $client = new Client();
        return $client->get($url)->getBody()->getContents();
    }
}