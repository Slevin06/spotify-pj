<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DJ tamayu')</title>
    @viteReactRefresh
    @vite([
    'resources/css/app.css',
    'resources/ts/app.tsx'
    ])
</head>
<body>
@yield('content')
{{--<div id="app"></div>--}}
</body>
</html>
