@if (!Session::isStarted()) Session::start(); @endif
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BlendBarometer - Meetinstrument voor blended onderwijs</title>
    <meta name="title" content="BlendBarometer - Meetinstrument voor blended onderwijs"/>
    <meta name="description"
          content="De BlendBarometer is een meetinstrument om de kwaliteit van de Blended module te meten en de kwalitatieve en harmonieuze mix van leeractiviteiten te beoordelen."/>
    <meta name="keywords"
          content="BlendBarometer, blend barometer, blended learning, blended onderwijs, blended module, blended leren, blended onderwijs kwaliteitsmeting, blended module kwaliteitsmeting, Avans, Avans Hogeschool, Avans BlendBarometer">

    <meta property="og:type" content="website"/>
    <meta property="og:url" content="https://blendbarometer.nl/"/>
    <meta property="og:title" content="BlendBarometer - Meetinstrument voor blended onderwijs"/>
    <meta property="og:description"
          content="De BlendBarometer is een meetinstrument om de kwaliteit van de Blended module te meten en de kwalitatieve en harmonieuze mix van leeractiviteiten te beoordelen."/>
    <meta property="og:image" content="https://blendbarometer.nl/images/og-thumbnail.png"/>

    <meta property="twitter:card" content="summary_large_image"/>
    <meta property="twitter:url" content="https://blendbarometer.nl/"/>
    <meta property="twitter:title" content="BlendBarometer - Meetinstrument voor blended onderwijs"/>
    <meta property="twitter:description"
          content="De BlendBarometer is een meetinstrument om de kwaliteit van de Blended module te meten en de kwalitatieve en harmonieuze mix van leeractiviteiten te beoordelen."/>
    <meta property="twitter:image" content="https://blendbarometer.nl/images/og-thumbnail.png"/>

    <meta name="robots" content="index, follow">

    <link rel="stylesheet" href={{ URL::asset('css/global.css') }}>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href={{ URL::asset('css/sidebar.css') }}>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    @yield('styles')
    @yield('scripts')
</head>
<body>
{{ $slot }}
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    function toggleDyslexiaMode() {
        const enabled = localStorage.getItem('dyslexiaMode') === 'enabled';
        localStorage.setItem('dyslexiaMode', enabled ? 'disabled' : 'enabled');
        document.body.classList.toggle('dyslexia-mode', !enabled);
    }

    if (localStorage.getItem('dyslexiaMode') === 'enabled') {
        document.body.classList.add('dyslexia-mode');
    }
</script>
</body>
</html>
