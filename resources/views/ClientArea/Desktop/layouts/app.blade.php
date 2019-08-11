<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @include('Common.header')

    @yield('additionalHead')
</head>
<body>
    <div id="app">
        <navigation v-if="this.$store.getters.isLoggedIn"></navigation>
        <user-guest-navigation v-else></user-guest-navigation>

        @yield('content')
        @include('Common.commonElements')
    </div>

    <script type="text/javascript">
        var userInformation = @json(\App\Utils\UserInformation::userInformation());
    </script>

    @include('Common.footer')
    @if(env('APP_DEBUG'))
        <script src="{{ asset('static/js/app.js') }}"></script>
    @else
        <script src="{{ asset('static/js/ccms.js') }}"></script>
    @endif
    @yield('additionalFooter')
</body>
</html>
