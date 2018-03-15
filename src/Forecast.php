<?php

namespace Codium\CleanCode;

use GuzzleHttp\Client;

class Forecast
{
    public function predict(string &$city, \DateTime $datetime = null, bool $wind = false): string
    {
        if (!$this->hasPredictionAvailableFor($datetime)) {
            return "";
        }

        // When date is not provided we look for the current prediction
        if (!$datetime) {
            $datetime = new \DateTime();
        }
        $woeid = $this->findCityId($city);

        $city = $woeid;

        // Find the predictions for the city
        $weatherUrl = "https://www.metaweather.com/api/location/$woeid";
        $response = $this->makeGetRequest($weatherUrl);
        $results = json_decode($response, true)['consolidated_weather'];
        foreach ($results as $result) {

            // When the date is the expected
            if ($result["applicable_date"] == $datetime->format('Y-m-d')) {
                // If we have to return the wind information
                if ($wind) {
                    return $result['wind_speed'];
                } else {
                    return $result['weather_state_name'];
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

    protected function findCityId(string &$city): string
    {
        $cityIdUrl = "https://www.metaweather.com/api/location/search/?query=$city";
        $response = $this->makeGetRequest($cityIdUrl);
        return json_decode($response, true)[0]['woeid'];
    }
}