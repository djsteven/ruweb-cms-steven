@php
    $sectionData = $contentJson['sections'][$sectionKey] ?? [];
@endphp

<div>
    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_section_heading') }}</label>
    <input type="text" name="content_json[sections][{{ $sectionKey }}][heading]"
           value="{{ $sectionData['heading'] ?? '' }}"
           class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50"
           placeholder="{{ __('admin.field_section_heading_ph') }}">
</div>

<div>
    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_section_body') }}</label>
    <textarea name="content_json[sections][{{ $sectionKey }}][body]" rows="4"
              class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50"
              placeholder="{{ __('admin.field_section_body_ph') }}">{{ $sectionData['body'] ?? '' }}</textarea>
</div>

<div>
    @include('admin.media._selector', [
        'name'  => "content_json[sections][{$sectionKey}][image_id]",
        'value' => $sectionData['image_id'] ?? null,
        'label' => __('admin.field_section_background_image'),
        'type'  => 'image',
    ])
</div>

