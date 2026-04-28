@extends('admin.layouts.app')

@section('title', __('admin.settings'))

@section('content')
<div class="mb-6">
    <h1 class="text-lg font-semibold text-white">{{ __('admin.settings') }}</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ __('admin.settings_subtitle') }}</p>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    {{-- Group tabs --}}
    @php $groupNames = $groups->keys(); @endphp
    <div class="flex gap-1 mb-6 border-b border-white/[0.06]">
        @foreach ($groupNames as $group)
            @php
                $groupKey = 'admin.settings_groups.' . $group;
                $groupLabel = __($groupKey) !== $groupKey ? __($groupKey) : str_replace('_', ' ', $group);
            @endphp
            <button type="button"
                    class="settings-tab px-3 py-2 text-sm font-medium border-b-2 transition-colors capitalize"
                    data-group="{{ $group }}"
                    onclick="switchTab('{{ $group }}')">
                {{ $groupLabel }}
            </button>
        @endforeach
    </div>

    {{-- Group panels --}}
    @foreach ($groups as $group => $settings)
        <div class="settings-panel space-y-4" data-group="{{ $group }}" style="display:none">
@foreach ($settings as $setting)
                @php
                    $labelKey = 'admin.settings_fields.' . $setting->key . '.label';
                    $helpKey = 'admin.settings_fields.' . $setting->key . '.help';
                    $label = __($labelKey) !== $labelKey
                        ? __($labelKey)
                        : str_replace('_', ' ', $setting->key);
                    $help = __($helpKey) !== $helpKey ? __($helpKey) : null;
                @endphp
                <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1 capitalize">
                        {{ $label }}
                    </label>
                    @if ($help)
                        <p class="text-xs text-gray-500 mb-2">{{ $help }}</p>
                    @endif

                    @if ($setting->type === 'select')
                        <select name="settings[{{ $setting->key }}]"
                                class="w-full bg-[#1a1a1a] border border-white/10 text-gray-400 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50">
                            @foreach ($setting->options ?? [] as $optValue => $optLabel)
                                @php
                                    $optKey = 'admin.settings_options.' . $setting->key . '.' . $optValue;
                                    $translatedOptLabel = __($optKey) !== $optKey ? __($optKey) : $optLabel;
                                @endphp
                                <option value="{{ $optValue }}" {{ $setting->value === $optValue ? 'selected' : '' }}>
                                    {{ $translatedOptLabel }}
                                </option>
                            @endforeach
                        </select>

                    @elseif ($setting->type === 'text')
                        <textarea name="settings[{{ $setting->key }}]" rows="3"
                                  class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50">{{ $setting->value }}</textarea>

                    @elseif ($setting->type === 'boolean')
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                            <input type="checkbox" name="settings[{{ $setting->key }}]" value="1"
                                   {{ $setting->value ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-700 peer-focus:ring-2 peer-focus:ring-sky-500/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sky-600"></div>
                        </label>

                    @elseif ($setting->type === 'integer')
                        <input type="number" name="settings[{{ $setting->key }}]"
                               value="{{ $setting->value }}"
                               class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50">

                    @elseif ($setting->type === 'media')
                        @include('admin.media._selector', [
                            'name' => 'settings[' . $setting->key . ']',
                            'value' => $setting->value,
                            'label' => '',
                        ])

                    @elseif ($setting->type === 'password')
                        @php $hasValue = ! empty($setting->value); @endphp
                        <div class="relative">
                            <input type="password" name="settings[{{ $setting->key }}]"
                                   autocomplete="new-password"
                                   placeholder="{{ $hasValue ? __('admin.password_stored_placeholder') : '' }}"
                                   class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 pr-20 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50">
                            <button type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-400 hover:text-sky-400 px-2 py-1"
                                    onclick="togglePasswordVisibility(this)">
                                {{ __('admin.show') }}
                            </button>
                        </div>
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
        </div>
    @endforeach

    <div class="mt-6">
        <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition-colors">
            {{ __('admin.btn_save_changes') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    const tabs = document.querySelectorAll('.settings-tab');
    const panels = document.querySelectorAll('.settings-panel');

    const STORAGE_KEY = 'admin.settings.activeTab';

    function switchTab(group, persist = true) {
        tabs.forEach(tab => {
            if (tab.dataset.group === group) {
                tab.classList.add('border-sky-500', 'text-sky-400');
                tab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-300');
            } else {
                tab.classList.remove('border-sky-500', 'text-sky-400');
                tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-300');
            }
        });
        panels.forEach(panel => {
            panel.style.display = panel.dataset.group === group ? '' : 'none';
        });
        if (persist) {
            try { sessionStorage.setItem(STORAGE_KEY, group); } catch (e) {}
        }
    }

    if (tabs.length) {
        let initial = null;
        try { initial = sessionStorage.getItem(STORAGE_KEY); } catch (e) {}
        const valid = initial && Array.from(tabs).some(t => t.dataset.group === initial);
        switchTab(valid ? initial : tabs[0].dataset.group, false);
    }

    function togglePasswordVisibility(btn) {
        const input = btn.parentElement.querySelector('input');
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = @json(__('admin.hide'));
        } else {
            input.type = 'password';
            btn.textContent = @json(__('admin.show'));
        }
    }
</script>
@endpush
@endsection
