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

    public function getWeather($lat, $lon)
    {
        $url = "https://api.met.no/weatherapi/locationforecast/2.0/complete?lat={$lat}&lon={$lon}";
        $response = $this->client->get($url);
        return json_decode($response->getBody(), true);
    }

    public function getMarineForecast($lat, $lon)
    {
        if (!is_numeric($lat) || !is_numeric($lon) || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
            \Log::error("Invalid coordinates for marine forecast: lat={$lat}, lon={$lon}");
            return null;
        }

        $lat = round($lat, 4);
        $lon = round($lon, 4);

        try {
            $url = "https://marine-api.open-meteo.com/v1/marine?latitude={$lat}&longitude={$lon}&hourly=wave_height,wave_direction,wave_period,wind_wave_height,swell_wave_height,swell_wave_direction,swell_wave_period,water_temperature&wind_speed_unit=mph";
            \Log::info("Marine Forecast URL: {$url}");
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            \Log::error("Marine API Error: " . $e->getMessage());
            \Log::error("Response: " . $e->getResponse()->getBody()->getContents());
            return null;
        }
    }
}