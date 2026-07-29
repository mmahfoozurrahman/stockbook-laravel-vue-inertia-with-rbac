<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123a34">
    <title inertia>{{ config('app.name', 'Folio') }}</title>
    @routes
    @vite(['resources/js/app.js', 'resources/scss/app.scss'])
    @inertiaHead
</head>
<body>
@inertia
</body>
</html>
