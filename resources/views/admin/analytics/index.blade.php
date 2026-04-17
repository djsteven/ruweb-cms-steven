@extends('admin.layouts.app')

@section('title', __('admin.analytics'))

@section('content')
<div class="mb-6">
    <h1 class="text-lg font-semibold text-white">{{ __('admin.analytics') }}</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ __('admin.analytics_subtitle') }}</p>
</div>

<form method="POST" action="{{ route('admin.analytics.update') }}" class="space-y-6">
    @csrf
    @method('PUT')

    <section class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div>
                <h2 class="text-base font-semibold text-white">{{ __('admin.analytics_google_title') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('admin.analytics_google_help') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="https://analytics.google.com/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-white/[0.04] text-gray-300 hover:bg-white/[0.08] transition-colors">{{ __('admin.analytics_google_analytics_link') }}</a>
                <a href="https://tagmanager.google.com/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-white/[0.04] text-gray-300 hover:bg-white/[0.08] transition-colors">{{ __('admin.analytics_google_tag_manager_link') }}</a>
            </div>
        </div>

        <div class="mt-4">
            <label for="google_tag_id" class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.analytics_google_field') }}</label>
            <p class="text-xs text-gray-500 mb-2">{{ __('admin.analytics_google_field_help') }}</p>
            <input id="google_tag_id" name="google_tag_id" type="text" value="{{ old('google_tag_id', $googleTagId) }}"
                   class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                   placeholder="G-XXXXXXXXXX">
            @error('google_tag_id')
                <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4 bg-[#101010] border border-white/[0.06] rounded-lg p-4">
            <p class="text-xs uppercase tracking-wide text-gray-600">{{ __('admin.analytics_saved_value') }}</p>
            <p class="text-sm text-gray-300 mt-1">{{ $googleTagId !== '' ? $googleTagId : __('admin.analytics_not_configured') }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ __('admin.analytics_google_summary') }}</p>
        </div>
    </section>

    <section class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div>
                <h2 class="text-base font-semibold text-white">{{ __('admin.analytics_meta_title') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('admin.analytics_meta_help') }}</p>
            </div>
            <a href="https://business.facebook.com/events_manager2/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-white/[0.04] text-gray-300 hover:bg-white/[0.08] transition-colors">{{ __('admin.analytics_meta_events_manager_link') }}</a>
        </div>

        <div class="mt-4">
            <label for="meta_pixel_id" class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.analytics_meta_field') }}</label>
            <p class="text-xs text-gray-500 mb-2">{{ __('admin.analytics_meta_field_help') }}</p>
            <input id="meta_pixel_id" name="meta_pixel_id" type="text" inputmode="numeric" value="{{ old('meta_pixel_id', $metaPixelId) }}"
                   class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                   placeholder="123456789012345">
            @error('meta_pixel_id')
                <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4 bg-amber-500/10 border border-amber-500/20 rounded-lg p-4">
            <p class="text-sm text-amber-200">{{ __('admin.analytics_meta_disclaimer') }}</p>
        </div>

        <div class="mt-4 bg-[#101010] border border-white/[0.06] rounded-lg p-4">
            <p class="text-xs uppercase tracking-wide text-gray-600">{{ __('admin.analytics_saved_value') }}</p>
            <p class="text-sm text-gray-300 mt-1">{{ $metaPixelId !== '' ? $metaPixelId : __('admin.analytics_not_configured') }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ __('admin.analytics_meta_summary') }}</p>
        </div>
    </section>

    <section class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div>
                <h2 class="text-base font-semibold text-white">{{ __('admin.analytics_search_console_title') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('admin.analytics_search_console_help') }}</p>
            </div>
            <a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-white/[0.04] text-gray-300 hover:bg-white/[0.08] transition-colors">{{ __('admin.analytics_search_console_link') }}</a>
        </div>

        <div class="mt-4">
            <label for="search_console_verification_token" class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.analytics_search_console_field') }}</label>
            <p class="text-xs text-gray-500 mb-2">{{ __('admin.analytics_search_console_field_help') }}</p>
            <input id="search_console_verification_token" name="search_console_verification_token" type="text" value="{{ old('search_console_verification_token', $searchConsoleVerificationToken) }}"
                   class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                   placeholder="abc123DEF456ghi789">
            @error('search_console_verification_token')
                <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4 bg-[#101010] border border-white/[0.06] rounded-lg p-4">
            <p class="text-xs uppercase tracking-wide text-gray-600">{{ __('admin.analytics_generated_tag') }}</p>
            <code class="block mt-2 text-xs text-emerald-300 break-all">&lt;meta name="google-site-verification" content="{{ $searchConsoleVerificationToken !== '' ? e($searchConsoleVerificationToken) : 'your-token' }}" /&gt;</code>
            <p class="text-xs text-gray-500 mt-2">{{ __('admin.analytics_search_console_summary') }}</p>
        </div>

        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            <div class="bg-[#101010] border border-white/[0.06] rounded-lg p-4">
                <h3 class="text-sm font-semibold text-white">{{ __('admin.analytics_search_console_html_title') }}</h3>
                <p class="text-sm text-gray-400 mt-2">{{ __('admin.analytics_search_console_html_help') }}</p>
            </div>
            <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-amber-100">{{ __('admin.analytics_search_console_dns_title') }}</h3>
                <p class="text-sm text-amber-200/90 mt-2">{{ __('admin.analytics_search_console_dns_help') }}</p>
            </div>
        </div>

        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            <div class="bg-[#101010] border border-white/[0.06] rounded-lg p-4">
                <p class="text-xs uppercase tracking-wide text-gray-600">{{ __('admin.analytics_search_console_html_steps_title') }}</p>
                <ol class="mt-3 space-y-2 text-sm text-gray-300 list-decimal list-inside">
                    <li>{{ __('admin.analytics_search_console_html_step_1') }}</li>
                    <li>{{ __('admin.analytics_search_console_html_step_2') }}</li>
                    <li>{{ __('admin.analytics_search_console_html_step_3') }}</li>
                </ol>
            </div>
            <div class="bg-[#101010] border border-white/[0.06] rounded-lg p-4">
                <p class="text-xs uppercase tracking-wide text-gray-600">{{ __('admin.analytics_search_console_dns_steps_title') }}</p>
                <ol class="mt-3 space-y-2 text-sm text-gray-300 list-decimal list-inside">
                    <li>{{ __('admin.analytics_search_console_dns_step_1') }}</li>
                    <li>{{ __('admin.analytics_search_console_dns_step_2') }}</li>
                    <li>{{ __('admin.analytics_search_console_dns_step_3') }}</li>
                    <li>{{ __('admin.analytics_search_console_dns_step_4') }}</li>
                </ol>
            </div>
        </div>
    </section>

    <div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
            {{ __('admin.btn_save_changes') }}
        </button>
    </div>
</form>
@endsection
