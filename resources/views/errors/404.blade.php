@if(request()->is('admin/*'))
    @extends('admin.layouts.guest')

    @section('title', '404 Not Found')

    @section('content')
    <div class="text-center">
        <p class="text-xs font-medium text-emerald-500 uppercase tracking-widest mb-4">404</p>
        <h1 class="text-2xl font-semibold text-white">Page not found</h1>
        <p class="text-sm text-gray-500 mt-2">The page you're looking for doesn't exist.</p>
        <a href="{{ route('admin.dashboard') }}" class="inline-block mt-6 px-4 py-2 bg-[#1a1a1a] ring-1 ring-white/10 text-gray-400 hover:text-white rounded-md text-sm transition-colors">
            Back to Dashboard
        </a>
    </div>
    @endsection
@else
    @extends('layouts.public')

    @section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <p class="text-sm font-medium text-gray-400 uppercase tracking-widest mb-4">404</p>
        <h1 class="text-3xl font-bold text-gray-900">Page not found</h1>
        <p class="text-gray-600 mt-3">The page you're looking for doesn't exist or has been moved.</p>
        <a href="{{ route('home') }}" class="inline-block mt-8 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-md transition-colors">
            Back to Home
        </a>
    </div>
    @endsection
@endif
