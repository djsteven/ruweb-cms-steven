@extends('admin.layouts.app')

@section('title', __('admin.google_reviews'))

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
    <div>
        <h1 class="text-lg font-semibold text-white">{{ __('admin.google_reviews') }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ __('admin.google_reviews_subtitle') }}</p>
    </div>

    <div class="flex items-center gap-2">
        @if(!$hasApiKey || !$hasPlaceId)
            <a href="{{ route('admin.settings.index') }}?tab=general"
               class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-600/20 hover:bg-amber-600/30 text-amber-400 text-sm font-medium rounded-md border border-amber-600/30 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ __('admin.google_reviews_configure') }}
            </a>
        @endif

        @if($hasApiKey && $hasPlaceId)
            <form method="POST" action="{{ route('admin.google-reviews.sync') }}" id="sync-form">
                @csrf
                <button type="submit" id="sync-btn"
                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition-colors">
                    <svg class="w-4 h-4" id="sync-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('admin.google_reviews_sync') }}
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Alert de configuración faltante + guía --}}
@if(!$hasApiKey || !$hasPlaceId)
    <div class="mb-6 space-y-3">
        <div class="rounded-xl bg-amber-500/10 border border-amber-500/20 p-4 flex gap-3">
            <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-amber-400">
                    @if(!$hasApiKey && !$hasPlaceId)
                        {{ __('admin.google_reviews_missing_both') }}
                    @elseif(!$hasApiKey)
                        {{ __('admin.google_reviews_missing_api_key') }}
                    @else
                        {{ __('admin.google_reviews_missing_place_id') }}
                    @endif
                </p>
                <p class="text-sm text-amber-300/70 mt-0.5">
                    <a href="{{ route('admin.settings.index') }}?tab=general" class="underline hover:text-amber-300">
                        {{ __('admin.google_reviews_go_settings') }}
                    </a>
                </p>
            </div>
        </div>

        {{-- Guía desplegable --}}
        <details class="group rounded-xl bg-[#141414] ring-1 ring-white/[0.06] overflow-hidden" id="serpapi-guide">
            <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer list-none select-none hover:bg-white/[0.02] transition-colors">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-sky-500/10 text-sky-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-medium text-gray-200">{{ __('admin.serpapi_guide_title') }}</span>
                </div>
                <svg class="w-4 h-4 text-gray-500 transition-transform duration-200 group-open:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </summary>

            <div class="px-5 pb-6 pt-2">
                <div class="border-t border-white/[0.06] pt-5 grid md:grid-cols-2 gap-8">

                    {{-- Parte 1: SerpAPI Key --}}
                    @if(!$hasApiKey)
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-[0.15em] text-sky-400 mb-4">
                            {{ __('admin.serpapi_guide_part1') }}
                        </h3>
                        <ol class="space-y-4">
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-sky-500/15 text-sky-400 text-xs font-bold shrink-0 mt-0.5">1</span>
                                <div>
                                    <p class="text-sm text-gray-300">{{ __('admin.serpapi_step1_title') }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ __('admin.serpapi_step1_desc') }}</p>
                                    <a href="https://serpapi.com/users/sign_up" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 mt-1.5 text-xs text-sky-400 hover:text-sky-300 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        serpapi.com/users/sign_up →
                                    </a>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-sky-500/15 text-sky-400 text-xs font-bold shrink-0 mt-0.5">2</span>
                                <div>
                                    <p class="text-sm text-gray-300">{{ __('admin.serpapi_step2_title') }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ __('admin.serpapi_step2_desc') }}</p>
                                    <a href="https://serpapi.com/manage-api-key" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 mt-1.5 text-xs text-sky-400 hover:text-sky-300 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        serpapi.com/manage-api-key →
                                    </a>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-sky-500/15 text-sky-400 text-xs font-bold shrink-0 mt-0.5">3</span>
                                <div>
                                    <p class="text-sm text-gray-300">{{ __('admin.serpapi_step3_title') }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ __('admin.serpapi_step3_desc') }}</p>
                                    <a href="{{ route('admin.settings.index') }}?tab=general"
                                       class="inline-flex items-center gap-1.5 mt-2 text-xs font-medium text-white bg-sky-600 hover:bg-sky-700 px-3 py-1.5 rounded-md transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ __('admin.google_reviews_go_settings') }}
                                    </a>
                                </div>
                            </li>
                        </ol>

                        {{-- Badge plan gratuito --}}
                        <div class="mt-5 rounded-lg bg-emerald-500/5 border border-emerald-500/15 px-4 py-3 flex items-start gap-3">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-xs font-semibold text-emerald-400">{{ __('admin.serpapi_free_plan_title') }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('admin.serpapi_free_plan_desc') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Parte 2: Place ID --}}
                    @if(!$hasPlaceId)
                    <div class="{{ !$hasApiKey ? '' : '' }}">
                        <h3 class="text-xs font-bold uppercase tracking-[0.15em] text-emerald-400 mb-4">
                            {{ __('admin.serpapi_guide_part2') }}
                        </h3>
                        <ol class="space-y-4">
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/15 text-emerald-400 text-xs font-bold shrink-0 mt-0.5">1</span>
                                <div>
                                    <p class="text-sm text-gray-300">{{ __('admin.serpapi_placeid_step1_title') }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ __('admin.serpapi_placeid_step1_desc') }}</p>
                                    <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 mt-1.5 text-xs text-emerald-400 hover:text-emerald-300 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Place ID Finder →
                                    </a>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/15 text-emerald-400 text-xs font-bold shrink-0 mt-0.5">2</span>
                                <div>
                                    <p class="text-sm text-gray-300">{{ __('admin.serpapi_placeid_step2_title') }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ __('admin.serpapi_placeid_step2_desc') }}</p>
                                    <div class="mt-1.5 rounded-lg bg-[#1a1a1a] border border-white/10 px-3 py-2">
                                        <code class="text-xs text-emerald-400 font-mono">ChIJN1t_tDeuEmsRUsoyG83frY4</code>
                                    </div>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/15 text-emerald-400 text-xs font-bold shrink-0 mt-0.5">3</span>
                                <div>
                                    <p class="text-sm text-gray-300">{{ __('admin.serpapi_placeid_step3_title') }}</p>
                                    <a href="{{ route('admin.settings.index') }}?tab=general"
                                       class="inline-flex items-center gap-1.5 mt-2 text-xs font-medium text-white bg-emerald-700 hover:bg-emerald-600 px-3 py-1.5 rounded-md transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ __('admin.google_reviews_go_settings') }}
                                    </a>
                                </div>
                            </li>
                        </ol>
                    </div>
                    @endif

                </div>
            </div>
        </details>
    </div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.google_reviews_stat_total') }}</p>
        <p class="mt-1 text-2xl font-semibold text-white">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.google_reviews_stat_visible') }}</p>
        <p class="mt-1 text-2xl font-semibold text-sky-400">{{ $stats['visible'] }}</p>
    </div>
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.google_reviews_stat_featured') }}</p>
        <p class="mt-1 text-2xl font-semibold text-amber-400">{{ $stats['featured'] }}</p>
    </div>
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.google_reviews_stat_avg') }}</p>
        <p class="mt-1 text-2xl font-semibold text-white flex items-center gap-1">
            {{ $stats['avg'] > 0 ? $stats['avg'] : '—' }}
            @if($stats['avg'] > 0)
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endif
        </p>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-2 mb-6">
    <select name="rating" onchange="this.form.submit()"
            class="bg-[#1a1a1a] border border-white/10 text-gray-400 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500/50">
        <option value="">{{ __('admin.google_reviews_filter_all_ratings') }}</option>
        @for ($i = 5; $i >= 1; $i--)
            <option value="{{ $i }}" {{ $currentRating == $i ? 'selected' : '' }}>{{ $i }} ★</option>
        @endfor
    </select>
    <select name="visibility" onchange="this.form.submit()"
            class="bg-[#1a1a1a] border border-white/10 text-gray-400 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500/50">
        <option value="">{{ __('admin.google_reviews_filter_all') }}</option>
        <option value="visible" {{ $currentVisibility === 'visible' ? 'selected' : '' }}>{{ __('admin.google_reviews_filter_visible') }}</option>
        <option value="hidden" {{ $currentVisibility === 'hidden' ? 'selected' : '' }}>{{ __('admin.google_reviews_filter_hidden') }}</option>
    </select>
    @if($currentRating || $currentVisibility)
        <a href="{{ route('admin.google-reviews.index') }}"
           class="px-3 py-2 bg-gray-800 border border-white/10 text-gray-400 text-sm rounded-md hover:bg-gray-700 transition-colors">
            {{ __('admin.clear_filters') }}
        </a>
    @endif
</form>

@if ($reviews->isEmpty())
    <div class="text-center py-20 text-gray-600">
        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <p class="text-sm font-medium text-gray-500">{{ __('admin.google_reviews_empty') }}</p>
        <p class="text-sm text-gray-600 mt-1">{{ __('admin.google_reviews_empty_hint') }}</p>
    </div>
@else
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/[0.06]">
                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3">{{ __('admin.google_reviews_col_author') }}</th>
                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3 hidden sm:table-cell">{{ __('admin.google_reviews_col_rating') }}</th>
                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">{{ __('admin.google_reviews_col_review') }}</th>
                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3 hidden md:table-cell">{{ __('admin.google_reviews_col_date') }}</th>
                    <th class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3">{{ __('admin.google_reviews_col_visible') }}</th>
                    <th class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3">{{ __('admin.google_reviews_col_featured') }}</th>
                    <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3">{{ __('admin.col_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[0.04]">
                @foreach ($reviews as $review)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $review->avatarUrl() }}" alt="{{ $review->author_name }}"
                                     class="w-8 h-8 rounded-full object-cover bg-gray-800 shrink-0">
                                <div>
                                    <p class="text-sm font-medium text-white leading-tight">{{ $review->author_name }}</p>
                                    <p class="text-xs text-gray-600 hidden sm:block">{{ $review->relative_time_description }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <div class="flex items-center gap-0.5">
                                @for ($s = 1; $s <= 5; $s++)
                                    <svg class="w-4 h-4 {{ $s <= $review->rating ? 'text-yellow-400' : 'text-gray-700' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell max-w-xs">
                            <p class="text-sm text-gray-400 line-clamp-2 leading-relaxed">{{ $review->text ?: '—' }}</p>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="text-sm text-gray-500">{{ $review->review_time?->diffForHumans() ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button"
                                    class="toggle-visible inline-flex items-center justify-center w-8 h-8 rounded-full transition-colors {{ $review->is_visible ? 'bg-sky-500/20 text-sky-400 hover:bg-sky-500/30' : 'bg-white/5 text-gray-600 hover:bg-white/10 hover:text-gray-400' }}"
                                    data-id="{{ $review->id }}"
                                    data-url="{{ route('admin.google-reviews.toggle', $review) }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($review->is_visible)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    @endif
                                </svg>
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button"
                                    class="toggle-featured inline-flex items-center justify-center w-8 h-8 rounded-full transition-colors {{ $review->is_featured ? 'bg-amber-500/20 text-amber-400 hover:bg-amber-500/30' : 'bg-white/5 text-gray-600 hover:bg-white/10 hover:text-gray-400' }}"
                                    data-id="{{ $review->id }}"
                                    data-url="{{ route('admin.google-reviews.feature', $review) }}">
                                <svg class="w-4 h-4" fill="{{ $review->is_featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.google-reviews.destroy', $review) }}"
                                  onsubmit="return confirm('{{ __('admin.confirm_delete_review') }}')" class="inline-flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-600 hover:text-red-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $reviews->links() }}</div>
@endif

@push('scripts')
<script>
function handleToggle(btn, field) {
    fetch(btn.dataset.url, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        const isActive = data[field];
        if (field === 'is_visible') {
            btn.className = `toggle-visible inline-flex items-center justify-center w-8 h-8 rounded-full transition-colors ${isActive ? 'bg-sky-500/20 text-sky-400 hover:bg-sky-500/30' : 'bg-white/5 text-gray-600 hover:bg-white/10 hover:text-gray-400'}`;
            btn.innerHTML = isActive
                ? `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`
                : `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>`;
        } else {
            btn.className = `toggle-featured inline-flex items-center justify-center w-8 h-8 rounded-full transition-colors ${isActive ? 'bg-amber-500/20 text-amber-400 hover:bg-amber-500/30' : 'bg-white/5 text-gray-600 hover:bg-white/10 hover:text-gray-400'}`;
            btn.innerHTML = `<svg class="w-4 h-4" fill="${isActive ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>`;
        }
    });
}

document.querySelectorAll('.toggle-visible').forEach(btn => btn.addEventListener('click', () => handleToggle(btn, 'is_visible')));
document.querySelectorAll('.toggle-featured').forEach(btn => btn.addEventListener('click', () => handleToggle(btn, 'is_featured')));

const syncForm = document.getElementById('sync-form');
if (syncForm) {
    syncForm.addEventListener('submit', () => {
        const icon = document.getElementById('sync-icon');
        if (icon) icon.classList.add('animate-spin');
        const btn = document.getElementById('sync-btn');
        if (btn) btn.disabled = true;
    });
}
</script>
@endpush
@endsection
