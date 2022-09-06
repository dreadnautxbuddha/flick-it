<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    @include('common.styles')
</head>
    <body class="antialiased">
        <div class="main relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            <div class="container mx-auto">
                @include('common.navigation')

                <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                    @include('common.header')

                    @yield('content')

                    @include('common.footer')
                </div>
            </div>
        </div>
    </body>

    @include('common.scripts')

    @yield('additional-scripts')
</html>
