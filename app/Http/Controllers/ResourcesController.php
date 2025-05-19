<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use SimpleXMLElement;


class ResourcesController extends Controller
{
       public function earthquakes()
    {
        try {
            $earthquakes = Cache::remember('bgs_earthquakes', now()->addHours(1), function () {
                $response = Http::get('http://earthquakes.bgs.ac.uk/feeds/UK_Recent.xml');

                if (!$response->successful()) {
                    \Log::error('BGS GeoRSS request failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return [];
                }

                $xml = new SimpleXMLElement($response->body());
                $items = [];

                foreach ($xml->channel->item as $item) {
                    $description = (string) $item->description;
                    preg_match('/Origin date\/time: (.+?) ; Location: (.+?) ; Lat\/long: (.+?),(.+?) ; Depth: (\d+) km ; Magnitude: (.+)/', $description, $matches);

                    if ($matches) {
                        $items[] = [
                            'time' => Carbon::parse($matches[1])->toDateTimeString(),
                            'place' => $matches[2],
                            'latitude' => (float) $matches[3],
                            'longitude' => (float) $matches[4],
                            'depth' => (int) $matches[5],
                            'magnitude' => (float) $matches[6],
                            'link' => (string) $item->link,
                        ];
                    }
                }

                return $items;
            });

            $earthquakeData = array_map(function ($quake) {
                $highlight = stripos($quake['place'], 'Arran') !== false || stripos($quake['place'], 'Clyde') !== false;
                return [
                    'time' => $quake['time'],
                    'place' => $quake['place'],
                    'magnitude' => $quake['magnitude'],
                    'highlight' => $highlight,
                    'link' => $quake['link'],
                ];
            }, $earthquakes);

            $message = empty($earthquakes) ? 'No earthquakes recorded in the UK in the last 50 days.' : null;
            $copyright = 'Contains British Geological Survey materials © UKRI ' . date('Y') . '.';

            return view('resources.earthquakes', compact('earthquakeData', 'message', 'copyright'));
        } catch (\Exception $e) {
            \Log::error('BGS earthquake data processing failed', ['error' => $e->getMessage()]);
            $message = 'Unable to fetch earthquake data. Please try again later.';
            $copyright = 'Contains British Geological Survey materials © UKRI ' . date('Y') . '.';
            return view('resources.earthquakes', ['earthquakeData' => [], 'message' => $message, 'copyright' => $copyright]);
        }
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