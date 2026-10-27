<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'UD Medan')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 antialiased">
    <div class="flex h-screen overflow-hidden">
        {{-- Left Sidebar --}}
        @include('layouts.sidebar')

        {{-- Konten Kanan --}}
        <main class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </main>
    </div>
</body>

</html>
