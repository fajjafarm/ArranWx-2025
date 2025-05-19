<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class ResourcesController extends Controller
{
    public function earthquakes()
    {
        // Fetch UK earthquakes from USGS (last 50 days)
        $endTime = Carbon::now()->toDateTimeString();
        $startTime = Carbon::now()->subDays(50)->toDateTimeString();
        $response = Http::get('https://earthquake.usgs.gov/fdsnws/event/1/query', [
            'format' => 'geojson',
            'starttime' => $startTime,
            'endtime' => $endTime,
            'minlatitude' => 49,
            'maxlatitude' => 61,
            'minlongitude' => -11,
            'maxlongitude' => 2,
        ]);

        $earthquakes = Cache::remember('earthquakes', now()->addHours(1), function () use ($startTime, $endTime) {
    return Http::get('https://earthquake.usgs.gov/fdsnws/event/1/query', [
        'format' => 'geojson',
        'starttime' => $startTime,
        'endtime' => $endTime,
        'minlatitude' => 49,
        'maxlatitude' => 61,
        'minlongitude' => -11,
        'maxlongitude' => 2,
    ])->json()['features'];
});

        // Process earthquakes to extract relevant data
        $earthquakeData = array_map(function ($quake) {
            $place = $quake['properties']['place'] ?? 'Unknown';
            $highlight = stripos($place, 'Arran') !== false || stripos($place, 'Clyde') !== false;
            return [
                'time' => Carbon::createFromTimestampMs($quake['properties']['time'])->toDateTimeString(),
                'place' => $place,
                'magnitude' => $quake['properties']['mag'] ?? 'N/A',
                'highlight' => $highlight,
            ];
        }, $earthquakes);

        return view('resources.earthquakes', compact('earthquakeData'));
    }

            public function shipAis()
    {
        $mapParams = [
            'width' => '100%',
            'height' => '600',
            'latitude' => 55.6,
            'longitude' => -5.3,
            'zoom' => 9,
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
            'zoom' => 10,
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
            'zoom' => 10,
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
            'zoom' => 10,
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