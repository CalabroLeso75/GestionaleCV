<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap Italia CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-italia@2.9.0/dist/css/bootstrap-italia.min.css" rel="stylesheet">
    <!-- Bootstrap Italia JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-italia@2.9.0/dist/js/bootstrap-italia.bundle.min.js"></script>
</head>
<body>
    @include('layouts.navigation')

    <!-- Page Content -->
    <main class="container my-5">
        @if (isset($header))
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">{{ $header }}</h1>
            </div>
        @endif

        {{ $slot }}
    </main>
    
</body>
</html>
