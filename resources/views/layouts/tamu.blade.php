{{-- Kerangka halaman sebelum login: login, daftar, lupa password --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Masuk') — SIPALA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        biru: { DEFAULT: '#2563EB', gelap: '#1D4ED8' },
                        tosca: { DEFAULT: '#0E8C7B', gelap: '#0B7365' },
                        navy: '#0B1C2C',
                        tinta: '#16223A',
                        kabut: '#64748B',
                        garis: '#E2E8F0',
                        kertas: '#F8FAFC',
                    },
                    fontFamily: { sans: ['Poppins', 'system-ui', 'sans-serif'] },
                },
            },
        }
    </script>
    <style>
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
    </style>
</head>
<body class="h-full bg-kertas font-sans text-tinta antialiased">
    @yield('konten')
</body>
</html>
