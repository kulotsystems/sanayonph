<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ env('APP_NAME') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link href="{{ asset('images/favicon.png') }}" rel="icon" type="image/vnd.microsoft.icon" />
</head>
<body>
    <div id="main"></div>
    <div id="fb-root"></div>
    <div id="fb-customer-chat" class="fb-customerchat"></div>
    <script src="{{ mix('js/vendor/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor/packages/vue.js') }}"></script>
    <script src="{{ mix('js/vendor/packages/roboto.js') }}"></script>
    <script src="{{ mix('js/vendor/packages/material-icons.js') }}"></script>
    <script src="{{ mix('js/vendor/packages/vuetify.js') }}"></script>
    <script src="{{ mix('js/vendor/packages/axios.js') }}"></script>
    <script src="{{ mix('js/vendor/packages/date-fns.js') }}"></script>
    <script src="{{ mix('js/vendor/packages/pace.js') }}"></script>
    <script src="{{ mix('js/vendor/vendor.js') }}"></script>
    <script src="{{ mix('js/sanayon.js') }}"></script>
</body>
</html>
