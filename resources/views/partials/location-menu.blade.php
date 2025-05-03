<li class="side-nav-title">Weather Forecasts</li>

@php
    // Fetch locations and group by type
    $locations = App\Models\Location::all()->groupBy('type');
    $types = ['Village', 'Marine', 'Hill'];
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
            </div>
        </li>
    @endif
@endforeach