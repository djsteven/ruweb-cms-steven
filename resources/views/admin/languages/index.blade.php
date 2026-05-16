@extends('admin.layouts.app')

@section('title', __('admin.languages'))

@section('content')
<div class="mb-6">
    <h1 class="text-lg font-semibold text-white">{{ __('admin.languages') }}</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ __('admin.languages_subtitle') }}</p>
</div>

<form method="POST" action="{{ route('admin.languages.update') }}" class="space-y-3">
    @csrf
    @method('PUT')

    @foreach($locales as $locale)
        <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4 flex items-center justify-between gap-4">
            <div>
                <div class="text-sm text-white font-medium">{{ $locale->name }} <span class="text-xs text-gray-500">{{ strtoupper($locale->code) }}</span></div>
                <div class="text-xs text-gray-600">{{ $locale->is_base ? __('admin.base_language') : __('admin.secondary_language') }}</div>
            </div>
            <div class="flex items-center gap-5">
                <label class="inline-flex items-center gap-2 text-sm text-gray-400">
                    <input type="hidden" name="locales[{{ $locale->code }}][is_active]" value="0">
                    <input type="checkbox" name="locales[{{ $locale->code }}][is_active]" value="1" {{ $locale->is_active ? 'checked' : '' }} {{ $locale->is_base ? 'disabled' : '' }}>
                    {{ __('admin.active') }}
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-400">
                    <input type="hidden" name="locales[{{ $locale->code }}][is_public]" value="0">
                    <input type="checkbox" name="locales[{{ $locale->code }}][is_public]" value="1" {{ $locale->is_public ? 'checked' : '' }} {{ $locale->is_base ? 'disabled' : '' }}>
                    {{ __('admin.public') }}
                </label>
            </div>
        </div>
    @endforeach

    <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition-colors">
        {{ __('admin.btn_save_changes') }}
    </button>
</form>

@if(!empty($available))
    <div class="mt-8">
        <h2 class="text-sm font-semibold text-white">{{ __('admin.add_language') }}</h2>
        <p class="text-xs text-gray-600 mt-0.5 mb-3">{{ __('admin.add_language_subtitle') }}</p>

        <form method="POST" action="{{ route('admin.languages.store') }}"
              class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4 flex items-center gap-3">
            @csrf
            <select name="code" class="bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2">
                @foreach($available as $code => $name)
                    <option value="{{ $code }}">{{ $name }} ({{ strtoupper($code) }})</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-white/[0.06] hover:bg-white/[0.1] text-white text-sm font-medium rounded-md transition-colors">
                {{ __('admin.add_language') }}
            </button>
        </form>
        @error('code')
            <p class="text-xs text-red-400 mt-2">{{ $message }}</p>
        @enderror
    </div>
@endif
@endsection
