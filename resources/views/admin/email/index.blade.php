@extends('admin.layouts.app')

@section('title', __('admin.email'))

@section('content')
<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h1 class="text-lg font-semibold text-white">{{ __('admin.email') }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ __('admin.email_subtitle') }}</p>
    </div>
    <button type="submit" form="email-form"
            class="shrink-0 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition-colors">
        {{ __('admin.btn_save_changes') }}
    </button>
</div>

@include('admin.settings._email_instructions')

<form id="email-form" method="POST" action="{{ route('admin.email.update') }}" class="mt-6 space-y-4">
    @csrf
    @method('PUT')

    @foreach ($settings as $setting)
        @php
            $labelKey = 'admin.settings_fields.' . $setting->key . '.label';
            $helpKey  = 'admin.settings_fields.' . $setting->key . '.help';
            $label    = __($labelKey) !== $labelKey ? __($labelKey) : str_replace('_', ' ', $setting->key);
            $help     = __($helpKey)  !== $helpKey  ? __($helpKey)  : null;
        @endphp
        <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
            <label class="block text-sm font-medium text-gray-300 mb-1 capitalize">{{ $label }}</label>
            @if ($help)
                <p class="text-xs text-gray-500 mb-2">{{ $help }}</p>
            @endif

            @if ($setting->type === 'boolean')
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                    <input type="checkbox" name="settings[{{ $setting->key }}]" value="1"
                           {{ $setting->value ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-9 h-5 bg-gray-700 peer-focus:ring-2 peer-focus:ring-sky-500/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sky-600"></div>
                </label>

            @elseif ($setting->type === 'password')
                @php $hasValue = ! empty($setting->value); @endphp
                <input type="password" name="settings[{{ $setting->key }}]"
                       autocomplete="new-password"
                       placeholder="{{ $hasValue ? __('admin.password_stored_placeholder') : '' }}"
                       class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50">
                @if ($hasValue)
                    <p class="mt-1.5 text-xs text-gray-500">{{ __('admin.password_stored_hint') }}</p>
                @endif

            @else
                <input type="text" name="settings[{{ $setting->key }}]"
                       value="{{ $setting->value }}"
                       class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50">
            @endif
        </div>
    @endforeach
</form>

{{-- Test email --}}
<div class="mt-6">
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
        <h3 class="text-sm font-semibold text-white mb-1">{{ __('admin.email_test_title') }}</h3>
        <p class="text-xs text-gray-500 mb-3">{{ __('admin.email_test_help') }}</p>
        <form method="POST" action="{{ route('admin.email.test') }}" class="flex gap-2">
            @csrf
            <input type="email" name="test_email" required
                   value="{{ auth()->user()->email }}"
                   placeholder="you@example.com"
                   class="flex-1 bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50">
            <button type="submit" class="px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white text-sm font-medium rounded-md transition-colors whitespace-nowrap">
                {{ __('admin.email_test_send') }}
            </button>
        </form>
    </div>
</div>

@endsection
