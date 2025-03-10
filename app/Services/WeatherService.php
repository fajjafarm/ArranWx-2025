<?php
namespace App\Services;

use GuzzleHttp\Client;

class WeatherService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => ['User-Agent' => 'ArranWeather/1.0'],
        ]);
    }

    // Weather data from Yr.no (for all locations)
    public function getWeather($lat, $lon)
    {
        $url = "https://api.met.no/weatherapi/locationforecast/2.0/complete?lat={$lat}&lon={$lon}";
        $response = $this->client->get($url);
        return json_decode($response->getBody(), true);
    }

    // Marine data from Open-Meteo (for Marine locations only)
    public function getMarineForecast($lat, $lon)
    {
        $url = "https://marine-api.open-meteo.com/v1/marine?latitude={$lat}&longitude={$lon}&hourly=wave_height,wave_direction,wave_period,swell_wave_height,swell_wave_direction,swell_wave_period,water_temperature";
        $response = $this->client->get($url);
        return json_decode($response->getBody(), true);
    }
}