<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', optional($menuList['metaInfo'])['title'] ?? null)</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon (Multiple formats for browser compatibility) -->
    @isset($menuList['metaInfo']['logoUrl'])
        <link rel="icon" href="{{ asset($menuList['metaInfo']['logoUrl']) }}" type="image/x-icon"> <!-- Standard ICO -->
        <link rel="icon" href="{{ asset($menuList['metaInfo']['logoUrl']) }}" type="image/png"> <!-- PNG fallback -->
        <link rel="apple-touch-icon" href="{{ asset($menuList['metaInfo']['logoUrl']) }}"> <!-- For iOS/Android -->
    @endisset

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.menuList = @json($menuList['subMenu']);
        window.baseURL = @json(URL::to('/'));
    </script>
    @stack('style')
</head>

<body>
    <div id="app" class="main">

        <!-- Topbar -->
        @include('layouts.topbar')

        <div class="main-container">
            <!-- Primary Sidebar -->
            @include('layouts.sidebar')


            <!-- Main Content -->
            <main class="content-area">
                @yield('content')
            </main>
        </div>
    </div>



        <!-- Bottom Status Bar -->
        @include('layouts.footer')
        @stack('scripts')
    @vite(['resources/js/scripts/script.js'])
    @vite(['resources/js/scripts/password_status.js'])
</body>
</html>
