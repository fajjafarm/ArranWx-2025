<li class="side-nav-title">Weather Forecasts</li>

@php
    // Fetch locations and group by type
    $locations = App\Models\Location::all()->groupBy('type')->map(function ($group) {
        return $group->sortBy('name');
    });
    $types = ['Village', 'Hill', 'Marine'];
@endphp

@foreach ($types as $index => $type)
    @if ($locations->has($type))
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#sidebar{{ $type }}" aria-expanded="false" aria-controls="sidebar{{ $type }}" class="side-nav-link">
                <span class="menu-icon">
                    <i class="ti {{ $type === 'Village' ? 'ti-home' : ($type === 'Marine' ? 'ti-anchor' : 'ti-trekking') }}"></i>
                </span>
                <span class="menu-text">{{ $type }} Forecasts</span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebar{{ $type }}">
                <ul class="sub-menu">
                    @foreach ($locations[$type] as $location)
                        <li class="side-nav-item">
                            <a href="{{ route('location.show', $location->name) }}" class="side-nav-link">
                                <span class="menu-text">{{ $location->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <ul class="sub-menu">
                    @foreach ($locations[$type] as $location)
                        <li class="side-nav-item">
                            <a href="{{ route('marine.show', $location->name) }}" class="side-nav-link">
                                <span class="menu-text">{{ $location->name }}</span>
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
        <span class="menu-icon">
            <i class="ti ti-database"></i>
        </span>
        <span class="menu-text">Resources</span>
        <span class="menu-arrow"></span>
    </a>
    <div class="collapse" id="sidebarResources">
        <ul class="sub-menu">
            <li class="side-nav-item">
                <a href="{{ route('resources.earthquakes') }}" class="side-nav-link">
                    <span class="menu-text">UK Earthquakes</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarShipAIS" aria-expanded="false" aria-controls="sidebarShipAIS" class="side-nav-link">
                    <span class="menu-text">Ship AIS Map</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarShipAIS">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('resources.ship-ais') }}" class="side-nav-link">
                                <span class="menu-text">All Ships Near Arran</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('resources.ship-catriona') }}" class="side-nav-link">
                                <span class="menu-text">MV Catriona</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('resources.ship-glen-sannox') }}" class="side-nav-link">
                                <span class="menu-text">MV Glen Sannox</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('resources.ship-alfred') }}" class="side-nav-link">
                                <span class="menu-text">MV Alfred</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('resources.flight-radar') }}" class="side-nav-link">
                    <span class="menu-text">Planes Near Arran</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('resources.lightning') }}" class="side-nav-link">
                    <span class="menu-text">Lightning Strikes</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('resources.tides') }}" class="side-nav-link">
                    <span class="menu-text">Tide Charts</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('resources.webcams') }}" class="side-nav-link">
                    <span class="menu-text">Arran Webcams</span>
                </a>
            </li>
            <li class="side-nav-item">
    <a href="{{ route('resources.aurora') }}" class="side-nav-link">
        <span class="menu-text">Northern Lights</span>
    </a>
</li>
        </ul>
    </div>
</li>