<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $posBrand['name']) · {{ $posBrand['name'] }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <style>
        body { background: #faf6f0; color: #1f1d2b; }

        :root {
            --brand: {{ $posBrand['primary_color'] ?? '#f97316' }};
            --brand-soft: {{ $posBrand['primary_color'] ?? '#f97316' }}1a;
        }

        .btn { display:inline-flex; align-items:center; gap:.5rem; padding:.7rem 1.1rem; font-weight:600; border-radius:.75rem; transition: all .15s ease; }
        .btn-primary { background: var(--brand); color:white; }
        .btn-primary:hover { filter: brightness(.92); box-shadow: 0 6px 18px -6px color-mix(in srgb, var(--brand) 60%, transparent); transform: translateY(-1px); }
        .btn-ghost { background:white; color:#1f1d2b; border:1px solid #e7e5e4; }
        .btn-ghost:hover { background:#f5f5f4; }
        .btn-danger { background:#e11d48; color:white; }
        .btn-danger:hover { background:#be123c; }
        .chip { display:inline-flex; padding:.2rem .65rem; border-radius:999px; font-size:.72rem; font-weight:600; letter-spacing:.02em; text-transform:uppercase; }
        .chip-green { background:#dcfce7; color:#166534; }
        .chip-yellow { background:#fef3c7; color:#92400e; }
        .chip-red { background:#fee2e2; color:#991b1b; }
        .chip-gray { background:#e5e7eb; color:#374151; }
        .input { width:100%; border:1px solid #e7e5e4; border-radius:.7rem; padding:.7rem .9rem; background:white; font-size:1rem; }
        .input:focus { outline:none; border-color: var(--brand); box-shadow:0 0 0 3px color-mix(in srgb, var(--brand) 18%, transparent); }
        .label { display:block; font-size:.82rem; font-weight:600; color:#44403c; margin-bottom:.35rem; }
        .card { background:white; border-radius:1.25rem; box-shadow:0 6px 20px -8px rgba(15,23,42,.12); }
        .nav-link { display:flex; align-items:center; gap:.65rem; padding:.7rem .9rem; border-radius:.75rem; color:#44403c; font-weight:500; }
        .nav-link.active, .nav-link:hover { background: var(--brand-soft); color: var(--brand); }

        /* Tenant primary color overrides applied to existing brand-* utilities used in views */
        .bg-brand-500 { background-color: var(--brand) !important; }
        .bg-brand-600 { background-color: var(--brand) !important; filter: brightness(.92); }
        .text-brand-600, .text-brand-500 { color: var(--brand) !important; }
        .from-brand-500 { --tw-gradient-from: var(--brand) !important; --tw-gradient-to: rgb(0 0 0 / 0) !important; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to) !important; }
        .border-brand-500 { border-color: var(--brand) !important; }

        @media print {
            .no-print { display:none !important; }
            body { background:white; }
        }
    </style>
</head>
<body class="min-h-screen">
    @yield('body')
    @stack('scripts')
</body>
</html>