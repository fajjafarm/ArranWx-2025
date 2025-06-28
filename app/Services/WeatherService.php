<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WeatherService
{
    protected $baseUrl = 'https://api.met.no/weatherapi/';
    protected $userAgent = 'ArranWeatherStation/1.0 (contact: your-email@example.com)';

    /**
     * Fetch weather forecast from yr.no
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    public function getWeather($latitude, $longitude)
    {
        try {
            $url = $this->baseUrl . 'locationforecast/2.0/complete?' . http_build_query([
                'lat' => $latitude,
                'lon' => $longitude,
            ]);

            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
                'Accept' => 'application/json',
            ])->retry(3, 1000)->timeout(10)->get($url);

            if (!$response->successful()) {
                Log::error('yr.no weather API request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
                return null;
            }

            $data = $response->json();
            if (!isset($data['properties']['timeseries'])) {
                Log::error('Invalid yr.no weather response', ['response' => $data]);
                return null;
            }

            Log::info('Fetched weather data from yr.no', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'timeseries_count' => count($data['properties']['timeseries']),
            ]);

            return $data;
        } catch (\Exception $e) {
            Log::error('Failed to fetch weather data from yr.no', [
                'error' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
            return null;
        }
    }

    /**
     * Fetch marine forecast from yr.no
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    public function getMarineForecast($latitude, $longitude)
    {
        try {
            $url = $this->baseUrl . 'oceanforecast/2.0/complete?' . http_build_query([
                'lat' => $latitude,
                'lon' => $longitude,
            ]);

            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
                'Accept' => 'application/json',
            ])->retry(3, 1000)->timeout(10)->get($url);

            if (!$response->successful()) {
                Log::error('yr.no marine API request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
                return null;
            }

            $data = $response->json();
            if (!isset($data['properties']['timeseries'])) {
                Log::error('Invalid yr.no marine response', ['response' => $data]);
                return null;
            }

            Log::info('Fetched marine data from yr.no', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'timeseries_count' => count($data['properties']['timeseries']),
            ]);

            return $data;
        } catch (\Exception $e) {
            Log::error('Failed to fetch marine data from yr.no', [
                'error' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
            return null;
        }
    }

    /**
     * Fetch sunrise/sunset and moon data
     *
     * @param float $latitude
     * @param float $longitude
     * @param string $date
     * @return array
     */
    public function getSunriseSunset($latitude, $longitude, $date)
    {
        try {
            $url = $this->baseUrl . 'sunrise/2.0/sun?' . http_build_query([
                'lat' => $latitude,
                'lon' => $longitude,
                'date' => $date,
                'offset' => '+01:00', // Adjust for BST/GMT
            ]);

            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
                'Accept' => 'application/json',
            ])->retry(3, 1000)->timeout(10)->get($url);

            if (!$response->successful()) {
                Log::error('yr.no sunrise API request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
                return ['sunrise' => 'N/A', 'sunset' => 'N/A', 'moonrise' => 'N/A', 'moonset' => 'N/A', 'moonphase' => null];
            }

            $data = $response->json();
            $sunrise = $data['properties']['sunrise']['time'] ?? 'N/A';
            $sunset = $data['properties']['sunset']['time'] ?? 'N/A';
            $moonrise = $data['properties']['moonrise']['time'] ?? 'N/A';
            $moonset = $data['properties']['moonset']['time'] ?? 'N/A';
            $moonphase = $data['properties']['moonphase'] ?? null;

            Log::info('Fetched sun/moon data from yr.no', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'date' => $date,
            ]);

            return compact('sunrise', 'sunset', 'moonrise', 'moonset', 'moonphase');
        } catch (\Exception $e) {
            Log::error('Failed to fetch sun/moon data from yr.no', [
                'error' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'date' => $date,
            ]);
            return ['sunrise' => 'N/A', 'sunset' => 'N/A', 'moonrise' => 'N/A', 'moonset' => 'N/A', 'moonphase' => null];
        }
    }
}