@php
    $page = $page ?? null;
    $isEdit = $page !== null;
    $contentJson = old('content_json', $page?->content_json ?? []);
    $currentTemplate = old('template_key', $page?->template_key ?? 'default');
    $featuredImageId = old('featured_image', $page?->featuredImage()?->id);
@endphp

<form id="editor-form"
      method="POST"
      action="{{ $isEdit ? route('admin.pages.update', $page) : route('admin.pages.store') }}"
      class="flex flex-col h-full">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- Tab bar --}}
    <div class="flex-none flex border-b border-white/[0.06] -mx-5 px-5 mb-5">
        <button type="button" data-tab="general"
                class="form-tab active mr-1 px-3 py-2 text-xs font-medium rounded-t transition-colors">
            {{ __('admin.tab_general') }}
        </button>
        <button type="button" data-tab="content"
                class="form-tab mr-1 px-3 py-2 text-xs font-medium rounded-t transition-colors">
            {{ __('admin.tab_content') }}
        </button>
        <button type="button" data-tab="status"
                class="form-tab px-3 py-2 text-xs font-medium rounded-t transition-colors">
            {{ __('admin.tab_status') }}
        </button>
    </div>

    {{-- TAB: General --}}
    <div data-panel="general" class="form-panel space-y-5">

        <div>
            <label for="title" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_title') }}</label>
            <input type="text" name="title" id="title"
                   value="{{ old('title', $page?->title) }}"
                   class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                   placeholder="{{ __('admin.field_title_placeholder') }}" required>
            @error('title')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_slug') }}</label>
            <div class="flex items-center gap-1">
                <span class="text-gray-600 text-sm">/</span>
                <input type="text" name="slug" id="slug"
                       value="{{ old('slug', $page?->slug) }}"
                       class="flex-1 bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                       placeholder="{{ __('admin.field_slug_placeholder') }}" required>
            </div>
            @error('slug')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="template_key" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_template') }}</label>
            <select name="template_key" id="template_key"
                    class="w-full bg-[#1a1a1a] border border-white/10 text-gray-400 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50">
                @foreach ($templates as $key => $tmpl)
                    <option value="{{ $key }}" {{ $currentTemplate === $key ? 'selected' : '' }}>{{ $tmpl['name'] }}</option>
                @endforeach
            </select>
            @error('template_key')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            @include('admin.media._selector', [
                'name'  => 'featured_image',
                'value' => $featuredImageId,
                'label' => __('admin.field_featured_image'),
            ])
        </div>

    </div>

    {{-- TAB: Contenido --}}
    <div data-panel="content" class="form-panel hidden space-y-4">

        <div id="section-fields" class="space-y-4">
            @foreach ($templates as $tKey => $tConfig)
                @foreach ($tConfig['sections'] as $section)
                    <div class="template-section bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4 space-y-4"
                         data-template="{{ $tKey }}"
                         style="{{ $tKey !== $currentTemplate ? 'display:none' : '' }}">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ str_replace('_', ' ', $section) }}</h3>
                            <label class="relative inline-flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="content_json[sections][{{ $section }}][is_visible]" value="0">
                                <input type="checkbox"
                                       name="content_json[sections][{{ $section }}][is_visible]"
                                       value="1"
                                       class="sr-only peer"
                                       {{ ($contentJson['sections'][$section]['is_visible'] ?? 1) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-gray-700 peer-checked:bg-emerald-500 rounded-full transition-colors relative
                                            after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                            after:bg-white after:rounded-full after:h-4 after:w-4
                                            after:transition-transform peer-checked:after:translate-x-4"></div>
                                <span class="text-xs text-gray-500">{{ __('admin.field_section_visible') }}</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_section_heading') }}</label>
                            <input type="text" name="content_json[sections][{{ $section }}][heading]"
                                   value="{{ $contentJson['sections'][$section]['heading'] ?? '' }}"
                                   class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                                   placeholder="{{ __('admin.field_section_heading_ph') }}">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_section_body') }}</label>
                            <textarea name="content_json[sections][{{ $section }}][body]" rows="4"
                                      class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                                      placeholder="{{ __('admin.field_section_body_ph') }}">{{ $contentJson['sections'][$section]['body'] ?? '' }}</textarea>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>

    </div>

    {{-- TAB: Estado --}}
    <div data-panel="status" class="form-panel hidden space-y-5">

        {{-- Publish --}}
        <div class="space-y-3">
            <div>
                <label for="status" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_status') }}</label>
                <select name="status" id="status"
                        class="w-full bg-[#1a1a1a] border border-white/10 text-gray-400 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50">
                    @foreach (config('cms.statuses') as $s)
                        <option value="{{ $s }}" {{ old('status', $page?->status ?? 'draft') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($isEdit && $page->published_at)
                <p class="text-xs text-gray-600">{{ __('admin.published_at', ['date' => $page->published_at->diffForHumans()]) }}</p>
            @endif
        </div>

        {{-- SEO --}}
        <div class="space-y-3">
            <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('admin.seo') }}</h3>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_meta_description') }}</label>
                <textarea name="content_json[meta][description]" rows="2"
                          class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                          placeholder="{{ __('admin.field_meta_description_ph') }}">{{ $contentJson['meta']['description'] ?? '' }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    {{ __('admin.field_og_title') }} <span class="text-gray-700">({{ __('admin.optional') }})</span>
                </label>
                <input type="text" name="content_json[meta][og_title]"
                       value="{{ $contentJson['meta']['og_title'] ?? '' }}"
                       class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                       placeholder="{{ __('admin.field_og_title_ph') }}">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    {{ __('admin.field_og_description') }} <span class="text-gray-700">({{ __('admin.optional') }})</span>
                </label>
                <input type="text" name="content_json[meta][og_description]"
                       value="{{ $contentJson['meta']['og_description'] ?? '' }}"
                       class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
                       placeholder="{{ __('admin.field_og_description_ph') }}">
            </div>
        </div>

        {{-- Delete --}}
        @if($isEdit)
            <div class="pt-3 border-t border-white/[0.06]">
                <button type="button" id="delete-page-btn"
                        class="w-full px-3 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm font-medium rounded-md transition-colors">
                    {{ __('admin.btn_delete_page') }}
                </button>
            </div>
        @endif

    </div>

</form>

{{-- Delete form — outside editor-form to avoid nesting --}}
@if($isEdit)
    <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" id="delete-form" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif

@push('scripts')
<script>
    // Tab switching
    const formTabs = document.querySelectorAll('.form-tab');
    const formPanels = document.querySelectorAll('.form-panel');

    formTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;
            formTabs.forEach(t => t.classList.toggle('active', t === tab));
            formPanels.forEach(p => p.classList.toggle('hidden', p.dataset.panel !== target));
            updateTabStyles();
        });
    });

    function updateTabStyles() {
        formTabs.forEach(t => {
            if (t.classList.contains('active')) {
                t.classList.add('text-emerald-400', 'border-b-2', 'border-emerald-500');
                t.classList.remove('text-gray-500', 'border-transparent');
            } else {
                t.classList.remove('text-emerald-400', 'border-b-2', 'border-emerald-500');
                t.classList.add('text-gray-500', 'border-transparent');
            }
        });
    }
    updateTabStyles();

    // Template switcher
    const templateSelect = document.getElementById('template_key');
    const sectionBlocks = document.querySelectorAll('.template-section');

    templateSelect.addEventListener('change', () => {
        sectionBlocks.forEach(block => {
            block.style.display = block.dataset.template === templateSelect.value ? '' : 'none';
        });
    });

    // Slug auto-generation from title
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    let slugManuallyEdited = {{ $isEdit ? 'true' : 'false' }};

    slugInput.addEventListener('input', () => { slugManuallyEdited = true; });

    titleInput.addEventListener('input', () => {
        if (slugManuallyEdited) return;
        slugInput.value = titleInput.value
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    });

    // Delete button → hidden form
    const deleteBtn = document.getElementById('delete-page-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', () => {
            if (confirm('{{ __('admin.confirm_delete_page') }}')) {
                document.getElementById('delete-form').submit();
            }
        });
    }
</script>
@endpush
