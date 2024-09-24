<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Resty Alejo">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Identity Account Manager</title>

    <!-- icon -->
    <link rel="icon" href="{{ asset('img/logo.png') }}">

    <!-- font family -->
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Poppins">

    <!-- main -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script type="module" src="{{ asset('js/app.js') }}" defer></script>

    
</head>
<body>

    <div id="app"></div>

</body>
</html>