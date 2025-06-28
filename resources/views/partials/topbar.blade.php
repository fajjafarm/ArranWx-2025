<header class="bg-blue-600 text-white p-4 flex justify-between items-center">
    <div class="flex items-center">
        <button class="md:hidden mr-4" onclick="toggleSidebar()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <h1 class="text-2xl font-bold">Arran Weather Station</h1>
    </div>
    <nav class="hidden md:flex space-x-4">
        <a href="{{ route('weather.index') }}" class="hover:underline">Home</a>
        @foreach(\App\Models\ApiCache::getCached('locations') ?? \App\Models\Location::all()->toArray() as $location)
            <a href="{{ route('weather.show', $location['name']) }}" class="hover:underline">{{ $location['name'] }}</a>
        @endforeach
    </nav>
</header>