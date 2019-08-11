<!-- Styles -->
@if(env('APP_DEBUG'))
    <link href="{{ asset('static/css/app.css') }}" rel="stylesheet">
@else
    <link href="{{ asset('static/css/ccms.css') }}" rel="stylesheet">
@endif
<link href="{{ asset('static/bootstrap/css/bootstrap.css') }}" rel="stylesheet">
<link href="{{ asset('static/semantic-ui/semantic.min.css') }}" rel="stylesheet">
<link href="{{ asset('static/font-logos/font-logos.css') }}" rel="stylesheet">