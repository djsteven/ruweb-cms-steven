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
@endsection
