@extends('admin.layouts.app')

@section('title', __('admin.analytics'))

@section('content')
@php
    $googleConfigured = $googleTagId !== '';
    $metaConfigured = $metaPixelId !== '';
    $scConfigured = $searchConsoleVerificationToken !== '';
@endphp

<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h1 class="text-lg font-semibold text-white">{{ __('admin.analytics') }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ __('admin.analytics_subtitle') }}</p>
    </div>
    <button type="submit" form="analytics-form"
            class="shrink-0 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
        {{ __('admin.btn_save_changes') }}
    </button>
</div>

<form id="analytics-form" method="POST" action="{{ route('admin.analytics.update') }}">
    @csrf
    @method('PUT')

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 border-b border-white/[0.06]" role="tablist">
        @foreach ([
            ['key' => 'google', 'label' => __('admin.analytics_google_title'), 'configured' => $googleConfigured],
            ['key' => 'meta', 'label' => __('admin.analytics_meta_title'), 'configured' => $metaConfigured],
            ['key' => 'search', 'label' => __('admin.analytics_search_console_title'), 'configured' => $scConfigured],
        ] as $tab)
            <button type="button"
                    class="analytics-tab inline-flex items-center gap-2 px-3 py-2 text-sm font-medium border-b-2 transition-colors"
                    data-tab="{{ $tab['key'] }}"
                    onclick="switchAnalyticsTab('{{ $tab['key'] }}')">
                <span class="w-1.5 h-1.5 rounded-full {{ $tab['configured'] ? 'bg-emerald-400' : 'bg-gray-600' }}"></span>
                {{ $tab['label'] }}
            </button>
        @endforeach
    </div>

    {{-- Google tag --}}
    <div class="analytics-panel" data-tab="google" style="display:none">
        <section class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-6">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between mb-5">
                <div class="max-w-xl">
                    <h2 class="text-base font-semibold text-white">{{ __('admin.analytics_google_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('admin.analytics_google_help') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="https://analytics.google.com/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-white/[0.04] text-gray-300 hover:bg-white/[0.08] transition-colors">{{ __('admin.analytics_google_analytics_link') }}</a>
                    <a href="https://tagmanager.google.com/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-white/[0.04] text-gray-300 hover:bg-white/[0.08] transition-colors">{{ __('admin.analytics_google_tag_manager_link') }}</a>
                </div>
            </div>

            <label for="google_tag_id" class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.analytics_google_field') }}</label>
            <p class="text-xs text-gray-500 mb-2">{{ __('admin.analytics_google_field_help') }}</p>
            <input id="google_tag_id" name="google_tag_id" type="text" value="{{ old('google_tag_id', $googleTagId) }}"
                   class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                   placeholder="G-XXXXXXXXXX">
            @error('google_tag_id')
                <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
            @enderror

            <p class="text-xs text-gray-500 mt-4">{{ __('admin.analytics_google_summary') }}</p>

            <div class="mt-6">
                <details class="group rounded-lg border border-white/[0.06] bg-[#101010] open:bg-[#111]">
                    <summary class="flex items-center justify-between cursor-pointer list-none px-4 py-3">
                        <span class="text-sm font-medium text-white">{{ __('admin.analytics_google_steps_title') }}</span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="px-4 pb-4 pt-1 border-t border-white/[0.04]">
                        <ol class="space-y-1.5 text-sm text-gray-300 list-decimal list-inside marker:text-gray-600">
                            <li>{{ __('admin.analytics_google_step_1') }}</li>
                            <li>{{ __('admin.analytics_google_step_2') }}</li>
                            <li>{{ __('admin.analytics_google_step_3') }}</li>
                        </ol>
                    </div>
                </details>
            </div>
        </section>
    </div>

    {{-- Meta Pixel --}}
    <div class="analytics-panel" data-tab="meta" style="display:none">
        <section class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-6">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between mb-5">
                <div class="max-w-xl">
                    <h2 class="text-base font-semibold text-white">{{ __('admin.analytics_meta_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('admin.analytics_meta_help') }}</p>
                </div>
                <a href="https://business.facebook.com/events_manager2/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-white/[0.04] text-gray-300 hover:bg-white/[0.08] transition-colors">{{ __('admin.analytics_meta_events_manager_link') }}</a>
            </div>

            <label for="meta_pixel_id" class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.analytics_meta_field') }}</label>
            <p class="text-xs text-gray-500 mb-2">{{ __('admin.analytics_meta_field_help') }}</p>
            <input id="meta_pixel_id" name="meta_pixel_id" type="text" inputmode="numeric" value="{{ old('meta_pixel_id', $metaPixelId) }}"
                   class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                   placeholder="123456789012345">
            @error('meta_pixel_id')
                <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
            @enderror

            <p class="text-xs text-gray-500 mt-4">{{ __('admin.analytics_meta_summary') }}</p>

            <div class="mt-5 flex gap-3 rounded-lg border border-white/[0.06] bg-white/[0.02] p-3">
                <svg class="w-4 h-4 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
                <p class="text-xs text-gray-400 leading-relaxed">{{ __('admin.analytics_meta_disclaimer') }}</p>
            </div>

            <div class="mt-4">
                <details class="group rounded-lg border border-white/[0.06] bg-[#101010] open:bg-[#111]">
                    <summary class="flex items-center justify-between cursor-pointer list-none px-4 py-3">
                        <span class="text-sm font-medium text-white">{{ __('admin.analytics_meta_steps_title') }}</span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="px-4 pb-4 pt-1 border-t border-white/[0.04]">
                        <ol class="space-y-1.5 text-sm text-gray-300 list-decimal list-inside marker:text-gray-600">
                            <li>{{ __('admin.analytics_meta_step_1') }}</li>
                            <li>{{ __('admin.analytics_meta_step_2') }}</li>
                            <li>{{ __('admin.analytics_meta_step_3') }}</li>
                        </ol>
                    </div>
                </details>
            </div>
        </section>
    </div>

    {{-- Search Console --}}
    <div class="analytics-panel" data-tab="search" style="display:none">
        <section class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-6">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between mb-5">
                <div class="max-w-xl">
                    <h2 class="text-base font-semibold text-white">{{ __('admin.analytics_search_console_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('admin.analytics_search_console_help') }}</p>
                </div>
                <a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-white/[0.04] text-gray-300 hover:bg-white/[0.08] transition-colors">{{ __('admin.analytics_search_console_link') }}</a>
            </div>

            <label for="search_console_verification_token" class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.analytics_search_console_field') }}</label>
            <p class="text-xs text-gray-500 mb-2">{{ __('admin.analytics_search_console_field_help') }}</p>
            <input id="search_console_verification_token" name="search_console_verification_token" type="text" value="{{ old('search_console_verification_token', $searchConsoleVerificationToken) }}"
                   class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                   placeholder="abc123DEF456ghi789">
            @error('search_console_verification_token')
                <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
            @enderror

            <p class="text-xs text-gray-500 mt-4">{{ __('admin.analytics_search_console_summary') }}</p>

            <div class="mt-6">
                <details class="group rounded-lg border border-white/[0.06] bg-[#101010] open:bg-[#111]">
                    <summary class="flex items-center justify-between cursor-pointer list-none px-4 py-3">
                        <span class="text-sm font-medium text-white">{{ __('admin.analytics_search_console_steps_title') }}</span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="px-4 pb-4 pt-1 border-t border-white/[0.04]">
                        <ol class="space-y-1.5 text-sm text-gray-300 list-decimal list-inside marker:text-gray-600">
                            <li>{{ __('admin.analytics_search_console_html_step_1') }}</li>
                            <li>{{ __('admin.analytics_search_console_html_step_2') }}</li>
                            <li>{{ __('admin.analytics_search_console_html_step_3') }}</li>
                        </ol>
                    </div>
                </details>
            </div>
        </section>
    </div>
</form>

@push('scripts')
<script>
    const analyticsTabs = document.querySelectorAll('.analytics-tab');
    const analyticsPanels = document.querySelectorAll('.analytics-panel');

    function switchAnalyticsTab(key) {
        analyticsTabs.forEach(tab => {
            const active = tab.dataset.tab === key;
            tab.classList.toggle('border-emerald-500', active);
            tab.classList.toggle('text-white', active);
            tab.classList.toggle('border-transparent', !active);
            tab.classList.toggle('text-gray-500', !active);
            tab.classList.toggle('hover:text-gray-300', !active);
        });
        analyticsPanels.forEach(panel => {
            panel.style.display = panel.dataset.tab === key ? '' : 'none';
        });
        try { history.replaceState(null, '', '#' + key); } catch (e) {}
    }

    const initial = (location.hash || '').replace('#', '');
    const validTabs = Array.from(analyticsTabs).map(t => t.dataset.tab);
    switchAnalyticsTab(validTabs.includes(initial) ? initial : validTabs[0]);
</script>
@endpush
@endsection
