<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', optional($menuList['metaInfo'])['title'] ?? null)</title>

<head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <title>@yield('title', optional($menuList['metaInfo'])['title'] ?? null)</title>

      <!-- Favicon (Multiple formats for browser compatibility) -->
      @isset($menuList['metaInfo']['logoUrl'])
      <link rel="icon" href="{{ asset($menuList['metaInfo']['logoUrl']) }}" type="image/x-icon"> <!-- Standard ICO -->
      <link rel="icon" href="{{ asset($menuList['metaInfo']['logoUrl']) }}" type="image/png"> <!-- PNG fallback -->
      <link rel="apple-touch-icon" href="{{ asset($menuList['metaInfo']['logoUrl']) }}"> <!-- For iOS/Android -->
      @endisset
      <!-- Swiper CSS -->
      @vite(['resources/css/app.css', 'resources/js/app.js'])
      @vite(['resources/css/frontend.css'])

</head>

<body id="app" class="bg-gray-100 text-gray-800 overflow-x-hidden">
      @yield('content')
      @include('frontend.layouts.footer')

      <!-- Scripts -->
      @vite(['resources/js/scripts/frontend.js'])
      <div style="display: none" id="aboutusText">{{ $menuList['metaInfo']['aboutUs']  }}</div>

      @stack('fScripts')
      <script>
      // Auto-hide flash messages after 5 seconds
      document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                  const alerts = document.querySelectorAll('[role="alert"]');
                  alerts.forEach(alert => {
                        alert.style.transition = 'opacity 1s ease';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 1000);
                  });
            }, 5000);
      });
      </script>
</body>

</html>
