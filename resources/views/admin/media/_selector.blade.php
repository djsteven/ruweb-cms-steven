{{-- Media selector component for embedding in forms --}}
{{-- Usage: @include('admin.media._selector', ['name' => 'featured_image', 'value' => $mediaId]) --}}

@php
    $name = $name ?? 'media_id';
    $value = $value ?? null;
    $label = $label ?? __('admin.choose_file');
    $type = $type ?? null;
@endphp

<div class="media-selector" data-name="{{ $name }}" @if($type) data-type="{{ $type }}" @endif>
    <label class="block text-sm font-medium text-gray-300 mb-1">{{ $label }}</label>
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" class="media-selector-input">

    <div class="media-selector-preview mb-2">
        {{-- Preview populated by JS when media is selected --}}
    </div>

    <div class="flex gap-2">
        <button type="button" class="media-selector-btn px-3 py-2 bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-md hover:bg-gray-700 transition-colors" data-label="{{ __('admin.choose_file') }}">
            {{ __('admin.choose_file') }}
        </button>
        <button type="button" class="media-selector-clear px-3 py-2 bg-gray-800 border border-gray-700 text-gray-500 text-sm rounded-md hover:bg-gray-700 transition-colors {{ $value ? '' : 'hidden' }}">
            {{ __('admin.clear') }}
        </button>
    </div>
</div>
