@extends('admin.layouts.app')

@section('title', __('admin.dashboard'))

@section('content')
<div class="mb-6">
    <h1 class="text-lg font-semibold text-white">{{ __('admin.dashboard') }}</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ __('admin.welcome_back', ['name' => auth()->user()->name]) }}</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="{{ route('admin.pages.index') }}"
       class="block bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5 hover:ring-white/[0.12] transition-all">
        <div class="text-2xl font-semibold text-white">{{ $pageCount }}</div>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.total_pages') }}</div>
    </a>
    <a href="{{ route('admin.posts.index') }}"
       class="block bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5 hover:ring-white/[0.12] transition-all">
        <div class="text-2xl font-semibold text-white">{{ $postCount }}</div>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.total_posts') }}</div>
    </a>
    <a href="{{ route('admin.pages.index', ['status' => 'published']) }}"
       class="block bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5 hover:ring-white/[0.12] transition-all">
        <div class="text-2xl font-semibold text-emerald-400">{{ $publishedPageCount }}</div>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.published') }}</div>
    </a>
    <a href="{{ route('admin.posts.index', ['status' => 'published']) }}"
       class="block bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5 hover:ring-white/[0.12] transition-all">
        <div class="text-2xl font-semibold text-emerald-400">{{ $publishedPostCount }}</div>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.published_blog') }}</div>
    </a>
</div>

<div class="mt-4">
    <a href="{{ route('admin.media.index') }}"
       class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-[#141414] ring-1 ring-white/[0.06] text-sm text-gray-400 hover:text-gray-300 hover:ring-white/[0.12] transition-all">
        <span>{{ __('admin.media_files') }}: {{ $mediaCount }}</span>
    </a>
</div>

<div class="mt-6 bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-white">{{ __('admin.media_health') }}</h2>
        <a href="{{ route('admin.media.health') }}" class="text-xs text-emerald-400 hover:text-emerald-300 transition-colors">
            {{ __('admin.view_details') }}
        </a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
        <div class="bg-[#1a1a1a] rounded-lg p-3">
            <div class="text-gray-500">{{ __('admin.total_media') }}</div>
            <div class="text-white font-semibold mt-1">{{ $mediaHealth['total_media'] }}</div>
        </div>
        <div class="bg-[#1a1a1a] rounded-lg p-3">
            <div class="text-gray-500">{{ __('admin.raster_images') }}</div>
            <div class="text-white font-semibold mt-1">{{ $mediaHealth['raster_images'] }}</div>
        </div>
        <div class="bg-[#1a1a1a] rounded-lg p-3">
            <div class="text-gray-500">{{ __('admin.webp_coverage') }}</div>
            <div class="text-white font-semibold mt-1">{{ $mediaHealth['webp_coverage_percent'] }}%</div>
        </div>
        <div class="bg-[#1a1a1a] rounded-lg p-3">
            <div class="text-gray-500">{{ __('admin.responsive_coverage') }}</div>
            <div class="text-white font-semibold mt-1">{{ $mediaHealth['responsive_coverage_percent'] }}%</div>
        </div>
        <div class="bg-[#1a1a1a] rounded-lg p-3">
            <div class="text-gray-500">{{ __('admin.bytes_saved') }}</div>
            <div class="text-white font-semibold mt-1">{{ number_format($mediaHealth['bytes_saved']) }}</div>
        </div>
        <div class="bg-[#1a1a1a] rounded-lg p-3">
            <div class="text-gray-500">{{ __('admin.missing_files') }}</div>
            <div class="text-white font-semibold mt-1">{{ $mediaHealth['missing_files'] }}</div>
        </div>
    </div>
</div>
@endsection
