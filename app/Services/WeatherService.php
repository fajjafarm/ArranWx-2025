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
            $url = "https://marine-api.open-meteo.com/v1/marine?latitude={$lat}&longitude={$lon}&hourly=wave_height,wave_direction,wave_period,wind_wave_height,swell_wave_height,swell_wave_direction,swell_wave_period,sea_surface_temperature&wind_speed_unit=mph";
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            \Log::error("Marine API Error: " . $e->getMessage());
            return null;
        }
    }

    public function getSunriseSunset($lat, $lon, $date)
    {
        $url = "https://api.met.no/weatherapi/sunrise/3.0/sun?lat={$lat}&lon={$lon}&date={$date}&offset=+00:00"; // UTC offset
        try {
            $response = $this->client->get($url);
            $data = json_decode($response->getBody(), true);
            return [
                'sunrise' => $data['properties']['sunrise']['time'] ?? null,
                'sunset' => $data['properties']['sunset']['time'] ?? null,
            ];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            \Log::error("Sunrise API Error: " . $e->getMessage());
            return ['sunrise' => null, 'sunset' => null];
        }
    }
}