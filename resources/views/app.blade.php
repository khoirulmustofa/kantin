<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="title" content="{{ config('app.name', 'Koperasi NFBS Bogor') }}">
    <meta name="description" content="Layanan digital Koperasi Karyawan NFBS Bogor. Memudahkan pemenuhan kebutuhan santri, pegawai, dan wali santri Nurul Fikri Boarding School Bogor secara praktis dan amanah.">
    <meta name="keywords" content="koperasi nfbs bogor, nurul fikri boarding school bogor, belanja santri nfbs, kantin digital nfbs, koperasi sekolah bogor, kebutuhan santri bogor">
    <meta name="author" content="Koperasi Karyawan NFBS Bogor">
    <meta name="robots" content="index, follow">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Koperasi NFBS Bogor - Solusi Belanja Berkah">
    <meta property="og:description" content="Penuhi kebutuhan harian dan sekolah di Koperasi NFBS Bogor. Belanja mudah, amanah, dan terintegrasi untuk seluruh civitas NFBS Bogor.">
    <meta property="og:image" content="{{ asset('assets/images/og-image.jpg') }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Koperasi NFBS Bogor - Solusi Belanja Berkah">
    <meta property="twitter:description" content="Platform belanja digital Koperasi NFBS Bogor untuk kemudahan transaksi santri dan pegawai.">
    <meta property="twitter:image" content="{{ asset('assets/images/og-image.jpg') }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon-32x32.png') }}">

    <title inertia>{{ config('app.name', 'Koperasi NFBS Bogor') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>