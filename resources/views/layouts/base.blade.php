<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $posBrand['name']) · {{ $posBrand['name'] }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
            colors: {
              brand: {
                50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74',
                400: '#fb923c', 500: '#f97316', 600: '#ea580c', 700: '#c2410c',
                800: '#9a3412', 900: '#7c2d12'
              }
            },
            boxShadow: {
              card: '0 6px 20px -8px rgba(15,23,42,0.18)'
            }
          }
        }
      }
    </script>
    @stack('head')
    <style>
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        body { background: #faf6f0; color: #1f1d2b; }
        .btn { display:inline-flex; align-items:center; gap:.5rem; padding:.7rem 1.1rem; font-weight:600; border-radius:.75rem; transition: all .15s ease; }
        .btn-primary { background:#f97316; color:white; }
        .btn-primary:hover { background:#ea580c; box-shadow: 0 6px 18px -6px rgba(249,115,22,.6); transform: translateY(-1px); }
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
        .input:focus { outline:none; border-color:#f97316; box-shadow:0 0 0 3px rgba(249,115,22,.18); }
        .label { display:block; font-size:.82rem; font-weight:600; color:#44403c; margin-bottom:.35rem; }
        .card { background:white; border-radius:1.25rem; box-shadow:0 6px 20px -8px rgba(15,23,42,.12); }
        .nav-link { display:flex; align-items:center; gap:.65rem; padding:.7rem .9rem; border-radius:.75rem; color:#44403c; font-weight:500; }
        .nav-link.active, .nav-link:hover { background:#fff7ed; color:#c2410c; }
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
