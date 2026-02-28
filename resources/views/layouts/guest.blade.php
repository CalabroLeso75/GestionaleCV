<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- CDN Assets (Bypass build) -->
    {{-- @vite(['resources/scss/app.scss', 'resources/js/app.js']) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-italia@2.9.0/dist/css/bootstrap-italia.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-italia@2.9.0/dist/js/bootstrap-italia.bundle.min.js"></script>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="text-center mb-4">
                    <a href="/">
                        <svg class="icon icon-xl"><use href="{{ asset('svg/sprites.svg#it-code-circle') }}"></use></svg>
                        <h1 class="h3 mb-3 font-weight-normal">Gestionale CV</h1>
                    </a>
                </div>
                
                <div class="card-wrapper card-space">
                    <div class="card card-bg">
                        <div class="card-body">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
