<?php

namespace Codium\CleanCode;

class MetaWeatherForecast
{
    /** @var HttpClient */
    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function predictWeather(string $cityName, \DateTime $datetime = null)
    {
        $thePrediction = $this->predictionsByNameOnDate($cityName, $datetime);
        return $thePrediction['weather_state_name'];
    }

    public function predictWind(string $cityName, \DateTime $datetime = null)
    {
        $thePrediction = $this->predictionsByNameOnDate($cityName, $datetime);
        return $thePrediction['wind_speed'];
    }

    private function predictionsByNameOnDate(string $cityName, \DateTime $datetime = null): array
    {
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
        return $this->httpClient->get($url);
    }
}