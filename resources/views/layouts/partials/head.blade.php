<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', config('app.name').' — Portal Karir D3 TI')</title>
@if (View::hasSection('meta-description'))
<meta name="description" content="@yield('meta-description')">
@endif
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script nonce="{{ Vite::cspNonce() }}">
    document.documentElement.classList.replace('no-js', 'js');
    try {
        if (localStorage.getItem('motion-pref') === 'reduced') {
            document.documentElement.setAttribute('data-motion', 'reduced');
        }
    } catch (e) {}
</script>
@yield('head-extra')
