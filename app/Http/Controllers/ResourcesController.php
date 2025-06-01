<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Earthquake;
use Carbon\Carbon;
use SimpleXMLElement;

class ResourcesController extends Controller
{
    public function earthquakes()
    {
        try {
            $endTime = Carbon::now();
            $startTime = $endTime->copy()->subDays(60);

            // Clean up quakes older than 90 days
            Earthquake::where('time', '<', now()->subDays(90))->delete();

            // Cache earthquake data for 4 hours
            $earthquakeData = Cache::remember('bgs_earthquakes', now()->addHours(4), function () use ($startTime, $endTime) {
                try {
                    $response = Http::retry(3, 1000)->timeout(10)->get('https://quakes.bgs.ac.uk/feeds/MhSeismology.xml');

                    if (!$response->successful()) {
                        \Log::error('BGS GeoRSS request failed', [
                            'status' => $response->status(),
                            'body' => substr($response->body(), 0, 500),
                        ]);
                        return $this->fetchFromDatabase($startTime, $endTime);
                    }

                    $body = $response->body();
                    if (empty($body) || strpos($body, '<rss') === false) {
                        \Log::error('BGS feed is empty or invalid', ['body' => substr($body, 0, 500)]);
                        return $this->fetchFromDatabase($startTime, $endTime);
                    }

                    $xml = new SimpleXMLElement($body);
                    if (!isset($xml->channel->item)) {
                        \Log::warning('BGS feed has no items', ['xml' => (string) $xml->asXML()]);
                        return $this->fetchFromDatabase($startTime, $endTime);
                    }

                    $items = [];
                    foreach ($xml->channel->item as $item) {
                        $description = (string) $item->description;
                        preg_match('/Origin date\/time: (.+?) ; Location: (.+?) ; Lat\/long: ([-\d.]+),([-\d.]+)(?: ; Depth: (\d+) km)? ; Magnitude:\s*([-\d.]+)/', $description, $matches);

                        if ($matches) {
                            $quakeTime = Carbon::parse($matches[1]);
                            $quakeData = [
                                'time' => $quakeTime,
                                'place' => trim($matches[2]),
                                'latitude' => (float) $matches[3],
                                'longitude' => (float) $matches[4],
                                'depth' => isset($matches[5]) ? (int) $matches[5] : 0,
                                'magnitude' => (float) $matches[6],
                                'link' => (string) $item->link,
                            ];

                            Earthquake::updateOrCreate(
                                ['time' => $quakeTime, 'place' => $quakeData['place']],
                                $quakeData
                            );

                            if ($quakeTime->gte($startTime)) {
                                $items[] = $quakeData;
                            }
                        } else {
                            \Log::warning('Failed to parse BGS item', ['description' => $description]);
                            $title = (string) $item->title;
                            if (preg_match('/M ([-\d.]+) :(.+)/', $title, $titleMatches)) {
                                $quakeTime = Carbon::parse($item->pubDate);
                                $quakeData = [
                                    'time' => $quakeTime,
                                    'place' => trim($titleMatches[2]),
                                    'latitude' => (float) $item->{'geo:lat'},
                                    'longitude' => (float) $item->{'geo:long'},
                                    'depth' => 0,
                                    'magnitude' => (float) $titleMatches[1],
                                    'link' => (string) $item->link,
                                ];

                                Earthquake::updateOrCreate(
                                    ['time' => $quakeTime, 'place' => $quakeData['place']],
                                    $quakeData
                                );

                                if ($quakeTime->gte($startTime)) {
                                    $items[] = $quakeData;
                                }
                            }
                        }
                    }

                    \Log::info('BGS earthquake count fetched', ['count' => count($items)]);
                } catch (\Exception $e) {
                    \Log::error('BGS feed processing failed', ['error' => $e->getMessage()]);
                    return $this->fetchFromDatabase($startTime, $endTime);
                }

                return $this->fetchFromDatabase($startTime, $endTime);
            });

            // Sort by distance
            usort($earthquakeData, fn($a, $b) => $a['distance'] <=> $b['distance']);

            $message = empty($earthquakeData) ? 'No earthquakes recorded near Arran in the last 60 days.' : null;
            $copyright = 'Contains British Geological Survey materials © UKRI ' . date('Y') . '.';

            \Log::info('Earthquake data rendered', ['count' => count($earthquakeData)]);

            return view('resources.earthquakes', compact('earthquakeData', 'message', 'copyright'));
        } catch (\Exception $e) {
            \Log::error('Earthquake processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Cache::forget('bgs_earthquakes');
            $message = 'Unable to fetch or process earthquake data. Displaying cached data if available.';
            $copyright = 'Contains British Geological Survey materials © UKRI ' . date('Y') . '.';

            $earthquakeData = $this->fetchFromDatabase($startTime, $endTime);
            usort($earthquakeData, fn($a, $b) => $a['distance'] <=> $b['distance']);

            return view('resources.earthquakes', compact('earthquakeData', 'message', 'copyright'));
        }
    }

    protected function fetchFromDatabase($startTime, $endTime)
    {
        return Earthquake::whereBetween('time', [$startTime, $endTime])
            ->orderBy('time', 'desc')
            ->get()
            ->map(function ($quake) {
                $highlight = stripos($quake->place, 'Arran') !== false ||
                             stripos($quake->place, 'Clyde') !== false ||
                             $this->calculateDistance(55.6, -5.3, $quake->latitude, $quake->longitude) < 20;
                $distance = $this->calculateDistance(55.6, -5.3, $quake->latitude, $quake->longitude);
                return [
                    'time' => $quake->time->toDateTimeString(),
                    'place' => $quake->place,
                    'magnitude' => $quake->magnitude,
                    'distance' => round($distance),
                    'highlight' => $highlight,
                    'link' => $quake->link,
                    'latitude' => $quake->latitude,
                    'longitude' => $quake->longitude,
                ];
            })->toArray();
    }

    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 3958.8; // Miles
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1) * cos($lat2) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function aurora()
    {
        try {
            // Cache aurora forecast for 1 hour
            $auroraData = Cache::remember('aurora_forecast', now()->addHour(), function () {
                try {
                    $response = Http::retry(3, 1000)->timeout(10)->get('https://services.swpc.noaa.gov/products/noaa-planetary-k-index-forecast.json');
                    if (!$response->successful()) {
                        \Log::error('NOAA Kp forecast request failed', ['status' => $response->status()]);
                        return ['kp_forecast' => [], 'message' => 'Unable to fetch aurora forecast data.', 'max_kp' => 0];
                    }

                    $data = $response->json();
                    $kp_forecast = array_slice($data, 1); // Skip header
                    $forecast = [];
                    $max_kp = 0;

                    foreach ($kp_forecast as $entry) {
                        $time = Carbon::parse($entry[0]);
                        $kp = (float) $entry[1];
                        if ($time->isFuture() && $time->lte(now()->addDays(3))) {
                            $forecast[] = [
                                'time' => $time->toDateTimeString(),
                                'kp' => $kp,
                                'label' => $time->format('M d H:i'),
                            ];
                            $max_kp = max($max_kp, $kp);
                        }
                    }

                    // Aurora visibility message
                    $message = $this->getAuroraMessage($max_kp);

                    return ['kp_forecast' => $forecast, 'message' => $message, 'max_kp' => $max_kp];
                } catch (\Exception $e) {
                    \Log::error('Aurora forecast processing failed', ['error' => $e->getMessage()]);
                    return ['kp_forecast' => [], 'message' => 'Unable to fetch aurora forecast data.', 'max_kp' => 0];
                }
            });

            \Log::info('Aurora forecast rendered', ['kp_count' => count($auroraData['kp_forecast'])]);

            return view('resources.aurora', compact('auroraData'));
        } catch (\Exception $e) {
            \Log::error('Aurora processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $auroraData = ['kp_forecast' => [], 'message' => 'Unable to fetch aurora forecast data.', 'max_kp' => 0];
            return view('resources.aurora', compact('auroraData'));
        }
    }

    protected function getAuroraMessage($max_kp)
    {
        if ($max_kp >= 7) {
            return 'Strong geomagnetic storm (G3–G5) expected. Aurora may be visible across the UK, including southern England.';
        } elseif ($max_kp >= 5) {
            return 'Moderate geomagnetic activity (G1–G2) expected. Aurora likely visible in northern and central UK, including Scotland and Northern Ireland.';
        } elseif ($max_kp >= 4) {
            return 'Minor geomagnetic activity expected. Aurora may be visible in northern Scotland.';
        } else {
            return 'Low geomagnetic activity. Aurora unlikely to be visible in the UK, except possibly in far northern Scotland under clear skies.';
        }
    }
}