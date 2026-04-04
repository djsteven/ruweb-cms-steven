<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $siteName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/js/admin.js'])
</head>
<body class="bg-[#0a0a0a] text-gray-100 min-h-screen font-sans antialiased">

    @include('admin.partials.sidebar')

    {{-- Mobile header --}}
    <header class="lg:hidden fixed top-0 left-0 right-0 z-40 flex items-center justify-between px-4 h-12 bg-[#111111] border-b border-white/[0.06]">
        <span class="text-sm font-semibold text-white">{{ $siteName }}</span>
        <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </header>

    <main class="flex-1 p-6 pt-16 lg:pt-6 lg:ml-56 min-h-screen">
        @include('admin.partials.alerts')
        @yield('content')
    </main>

    @include('admin.media._library-modal')
    @include('admin.media._upload-modal')

    @stack('modals')
    @stack('scripts')

    <script>
        document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html>
