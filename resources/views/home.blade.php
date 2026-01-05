<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Fiszki</title>
    @vite('resources/js/app.js')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.Laravel = {
            user: @json(Auth::user())
        };
    </script>

</head>
<body>
<div id="app" data-page="home"></div>
</body>
</html>
