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

            // Cache database query results for 4 hours
            $earthquakeData = Cache::remember('bgs_earthquakes', now()->addHours(4), function () use ($startTime, $endTime) {
                // Fetch and update database
                try {
                    $response = Http::retry(3, 1000)->timeout(10)->get('https://quakes.bgs.ac.uk/feeds/MhSeismology.xml');

                    if (!$response->successful()) {
                        \Log::error('BGS MhSeismology GeoRSS request failed', [
                            'status' => $response->status(),
                            'body' => substr($response->body(), 0, 500),
                        ]);
                        return $this->fetchFromDatabase($startTime, $endTime);
                    }

                    $body = $response->body();
                    if (empty($body) || strpos($body, '<rss') === false) {
                        \Log::error('BGS MhSeismology feed is empty or invalid', ['body' => substr($body, 0, 500)]);
                        return $this->fetchFromDatabase($startTime, $endTime);
                    }

                    $xml = new SimpleXMLElement($body);
                    if (!isset($xml->channel->item)) {
                        \Log::warning('BGS MhSeismology feed has no items', ['xml' => (string) $xml->asXML()]);
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
                            \Log::warning('Failed to parse BGS MhSeismology item', ['description' => $description]);
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

                // Query database for all quakes in the time range
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

            // Sort by distance
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
                // Highlight for Arran, Clyde, or distance < 20 miles
                $highlight = stripos($quake->place, 'Arran') !== false ||
                             stripos($quake->place, 'Clyde') !== false ||
                             $this->calculateDistance(55.6, -5.3, $quake->latitude, $quake->longitude) < 20;
                // Calculate distance from Arran (55.6°N, -5.3°E)
                $distance = $this->calculateDistance(55.6, -5.3, $quake->latitude, $quake->longitude);
                return [
                    'time' => $quake->time->toDateTimeString(),
                    'place' => $quake->place,
                    'magnitude' => $quake->magnitude,
                    'distance' => round($distance), // Round to nearest mile
                    'highlight' => $highlight,
                    'link' => $quake->link,
                    'latitude' => $quake->latitude, // For Leaflet
                    'longitude' => $quake->longitude, // For Leaflet
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

            public function shipAis()
    {
        $mapParams = [
            'width' => '100%',
            'height' => '600',
            'latitude' => 55.6,
            'longitude' => -5.3,
            'zoom' => 10,
            'names' => true,
        ];
        $vesselLinks = [
            ['name' => 'MV Catriona', 'route' => 'resources.ship-catriona'],
            ['name' => 'MV Glen Sannox', 'route' => 'resources.ship-glen-sannox'],
            ['name' => 'MV Alfred', 'route' => 'resources.ship-alfred'],
        ];
        return view('resources.ship-ais', compact('mapParams', 'vesselLinks'));
    }

    public function shipCatriona()
    {
        $mapParams = [
            'width' => '100%',
            'height' => '600',
            'latitude' => 55.6,
            'longitude' => -5.3,
            'zoom' => 11,
            'mmsi' => '235116772',
            'names' => true,
        ];
        $vesselName = 'MV Catriona';
        return view('resources.ship-vessel', compact('mapParams', 'vesselName'));
    }

    public function shipGlenSannox()
    {
        $mapParams = [
            'width' => '100%',
            'height' => '600',
            'latitude' => 55.6,
            'longitude' => -5.3,
            'zoom' => 11,
            'mmsi' => '232049068',
            'names' => true,
        ];
        $vesselName = 'MV Glen Sannox';
        return view('resources.ship-vessel', compact('mapParams', 'vesselName'));
    }

    public function shipAlfred()
    {
        $mapParams = [
            'width' => '100%',
            'height' => '600',
            'latitude' => 55.6,
            'longitude' => -5.3,
            'zoom' => 11,
            'mmsi' => '232019501',
            'names' => true,
        ];
        $vesselName = 'MV Alfred';
        return view('resources.ship-vessel', compact('mapParams', 'vesselName'));
    }



    public function flightRadar()
    {
        // FlightRadar24 embed URL (free map centered on Arran)
        $mapUrl = 'https://www.flightradar24.com/55.6,-5.3/10';
        return view('resources.flight-radar', compact('mapUrl'));
    }

    public function lightning()
    {
        // Blitzortung embed URL (free lightning map centered on Arran)
        $mapUrl = 'https://www.blitzortung.org/en/live_lightning_maps.php?map=10';
        return view('resources.lightning', compact('mapUrl'));
    }

    public function tides()
    {
        // Tide data (simulated for Lamlash, Brodick, Lochranza)
        // Note: Replace with real tide API if available (e.g., TideAPI or UK Hydrographic Office)
        $locations = ['Lamlash', 'Brodick', 'Lochranza'];
        $tideData = [];

        foreach ($locations as $location) {
            // Simulated tide times for demonstration
            $tideData[$location] = [
                'date' => Carbon::today()->toDateString(),
                'high_tides' => [
                    ['time' => '06:30', 'height' => '3.2m'],
                    ['time' => '18:45', 'height' => '3.4m'],
                ],
                'low_tides' => [
                    ['time' => '12:15', 'height' => '0.8m'],
                    ['time' => '00:30', 'height' => '0.7m'],
                ],
            ];
        }

        return view('resources.tides', compact('tideData', 'locations'));
    }
public function webcams()
    {
        $webcams = [
            [
                'title' => 'Brodick Ferry Terminal (CMAL)',
                'url' => 'https://player.twitch.tv/?channel=cmalbrodick&parent=' . request()->getHost(),
                'source' => 'Caledonian Maritime Assets Ltd',
                'type' => 'iframe',
            ],
            [
                'title' => 'Lochranza Ferry Terminal (CMAL)',
                'url' => 'https://player.twitch.tv/?channel=cmallochranza&parent=' . request()->getHost(),
                'source' => 'Caledonian Maritime Assets Ltd',
                'type' => 'iframe',
            ],
            [
                'title' => 'B880 String Road Cam (West)',
                'url' => 'https://alerts.live-website.com/roadcamimages/2382_cam1.jpg',
                'source' => 'North Ayrshire Council',
                'type' => 'image',
            ],
            [
                'title' => 'B880 String Road Cam (East)',
                'url' => 'https://alerts.live-website.com/roadcamimages/2382_cam2.jpg',
                'source' => 'North Ayrshire Council',
                'type' => 'image',
            ],
            [
                'title' => 'Brodick Bay towards Goatfell',
                'url' => 'https://www.cottagesonarran.co.uk/arran-webcam/',
                'source' => 'Cottages on Arran',
                'type' => 'link',
            ],
            [
                'title' => 'Brodick Ferry Port',
                'url' => 'https://www.cottagesonarran.co.uk/arran-webcam/',
                'source' => 'Cottages on Arran',
                'type' => 'link',
            ],
        ];

        $nacRoadCamsLink = 'https://www.north-ayrshire.gov.uk/roads-and-parking/road-cams';

        return view('resources.webcams', compact('webcams', 'nacRoadCamsLink'));
    }
}