<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name='viewport' content='width=device-width, initial-scale=1.0,  user-scalable=0' />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>INSIDER LEAGUE </title>

    @include('layouts.styles')

</head>

<body>
    <div id="page_content">
        <div id="page_content_inner">
            @include('layouts.headers')
            @yield('content')
        </div>
    </div>
</body>
@include('layouts.scripts')
@yield('scripts')

</html>
