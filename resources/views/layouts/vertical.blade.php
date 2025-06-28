<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.title-meta')
    @include('layouts.partials.head-css')
</head>
<body data-sidebar="dark">
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.partials.topbar')
        @include('layouts.partials.sidenav')

        <!-- Content -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            @include('layouts.partials.footer')
        </div>
    </div>

    @include('layouts.partials.customizer')
    @include('layouts.partials.footer-scripts')
</body>
</html>