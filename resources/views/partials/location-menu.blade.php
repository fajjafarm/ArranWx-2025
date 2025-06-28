<li class="side-nav-title">Weather Forecasts</li>

@php
    // Fetch locations and group by type, ensuring it's a Collection
    $locations = \App\Models\ApiCache::getCached('locations');
    if (is_array($locations)) {
        $locations = collect($locations);
    } else {
        $locations = \App\Models\Location::all()->groupBy('type')->map(function ($group) {
            return $group->sortBy('name');
        });
    }
    $types = ['Village', 'Hill', 'Marine'];

    // Debug: Log the type and structure of $locations
    \Illuminate\Support\Facades\Log::info('Locations data in location-menu', [
        'type' => gettype($locations),
        'is_collection' => $locations instanceof \Illuminate\Support\Collection,
        'data' => $locations->toArray()
    ]);
@endphp

@foreach ($types as $index => $type)
    @if (collect($locations)->has($type))
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#sidebar{{ $type }}" aria-expanded="false" aria-controls="sidebar{{ $type }}" class="side-nav-link">
                <i class="ti {{ $type === 'Village' ? 'ti-home' : ($type === 'Marine' ? 'ti-anchor' : 'ti-trekking') }}"></i>
                <span>{{ $type }} Forecasts</span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebar{{ $type }}">
                <ul class="sub-menu">
                    @foreach ($locations[$type] as $location)
                        <li class="side-nav-item">
                            <a href="{{ route('weather.show', $location->name) }}" class="side-nav-link">
                                <span>{{ $location->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </li>
    @endif
@endforeach

<li class="side-nav-title">Resources</li>
<li class="side-nav-item">
    <a data-bs-toggle="collapse" href="#sidebarResources" aria-expanded="false" aria-controls="sidebarResources" class="side-nav-link">
        <i class="ti ti-database"></i>
        <span>Resources</span>
        <span class="menu-arrow"></span>
    </a>
    <div class="collapse" id="sidebarResources">
        <ul class="sub-menu">
            <li class="side-nav-item">
                <a href="{{ route('resources.earthquakes') }}" class="side-nav-link">
                    <span>UK Earthquakes</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarShipAIS" aria-expanded="false" aria-controls="sidebarShipAIS" class="side-nav-link">
                    <span>Ship AIS Map</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarShipAIS">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('resources.ship-ais') }}" class="side-nav-link">
                                <span>All Ships Near Arran</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('resources.ship-catriona') }}" class="side-nav-link">
                                <span>MV Catriona</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('resources.ship-glen-sannox') }}" class="side-nav-link">
                                <span>MV Glen Sannox</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('resources.ship-alfred') }}" class="side-nav-link">
                                <span>MV Alfred</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('resources.flight-radar') }}" class="side-nav-link">
                    <span>Planes Near Arran</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('resources.lightning') }}" class="side-nav-link">
                    <span>Lightning Strikes</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('resources.tide') }}" class="side-nav-link">
                    <span>Tide Charts</span>
                </a>
            </li>
            @if (isset($location) && $location->name)
                <li class="side-nav-item">
                    <a href="{{ route('tide.show', $location->name) }}" class="side-nav-link">
                        <span>{{ $location->name }} Tide Forecast</span>
                    </a>
                </li>
            @endif
            <li class="side-nav-item">
                <a href="{{ route('resources.webcams') }}" class="side-nav-link">
                    <span>Arran Webcams</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('resources.aurora') }}" class="side-nav-link">
                    <span>Northern Lights</span>
                </a>
            </li>
        </ul>
    </div>
</li>