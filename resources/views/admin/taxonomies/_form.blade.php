@php
    $taxonomy = $taxonomy ?? null;
    $isEdit = $taxonomy !== null;
@endphp

<form id="editor-form"
      method="POST"
      action="{{ $isEdit ? route('admin.taxonomies.update', [$type, $taxonomy]) : route('admin.taxonomies.store', $type) }}">
    @csrf
    @if($isEdit) @method('PUT') @endif
    <input type="hidden" name="locale" value="{{ old('locale', $taxonomy?->locale ?? \App\Models\Locale::baseCode()) }}">
    @include('admin.partials.stale-fields', ['staleFieldNames' => $staleFieldNames ?? []])

    <div class="space-y-5">

        <div>
            <label for="name" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_title') }}</label>
            <input type="text" name="name" id="tax-name"
                   value="{{ old('name', $taxonomy?->name) }}"
                   class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50"
                   placeholder="{{ __('admin.taxonomy_name_placeholder') }}" required>
            @error('name')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_slug') }}</label>
            <input type="text" name="slug" id="tax-slug"
                   value="{{ old('slug', $taxonomy?->slug) }}"
                   class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50"
                   placeholder="{{ __('admin.field_slug_placeholder') }}" required>
            @error('slug')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.taxonomy_description') }}</label>
            <textarea name="description" id="description" rows="3"
                      class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50"
                      placeholder="{{ __('admin.taxonomy_description_placeholder') }}">{{ old('description', $taxonomy?->description) }}</textarea>
            @error('description')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if($parents->isNotEmpty())
        <div>
            <label for="parent_id" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.taxonomy_parent') }}</label>
            <select name="parent_id" id="parent_id"
                    class="w-full bg-[#1a1a1a] border border-white/10 text-gray-400 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50">
                <option value="">— {{ __('admin.taxonomy_no_parent') }} —</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $taxonomy?->parent_id) == $parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        @endif

        <div>
            <label for="order" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.taxonomy_order') }}</label>
            <input type="number" name="order" id="order" min="0"
                   value="{{ old('order', $taxonomy?->order ?? 0) }}"
                   class="w-32 bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50">
            @error('order')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition-colors">
                {{ $isEdit ? __('admin.btn_save_changes') : __('admin.taxonomy_btn_create') }}
            </button>
            <a href="{{ route('admin.taxonomies.index', $type) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-medium rounded-md transition-colors">
                {{ __('admin.btn_cancel') }}
            </a>
        </div>

        @if($isEdit && auth()->user()->can('delete', $taxonomy))
            <div class="pt-3 border-t border-white/[0.06]">
                <button type="button" id="delete-taxonomy-btn"
                        class="px-3 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm font-medium rounded-md transition-colors">
                    {{ __('admin.taxonomy_btn_delete') }}
                </button>
            </div>
        @endif

    </div>
</form>

@if($isEdit && auth()->user()->can('delete', $taxonomy))
    <form method="POST" action="{{ route('admin.taxonomies.destroy', [$type, $taxonomy]) }}" id="delete-taxonomy-form" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif

@push('scripts')
<script>
    const taxNameInput = document.getElementById('tax-name');
    const taxSlugInput = document.getElementById('tax-slug');
    let slugManuallyEdited = {{ $isEdit ? 'true' : 'false' }};

    taxSlugInput.addEventListener('input', () => { slugManuallyEdited = true; });

    taxNameInput.addEventListener('input', () => {
        if (slugManuallyEdited) return;
        taxSlugInput.value = taxNameInput.value
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    });

    const deleteBtn = document.getElementById('delete-taxonomy-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', () => {
            if (confirm('{{ __('admin.taxonomy_confirm_delete') }}')) {
                document.getElementById('delete-taxonomy-form').submit();
            }
        });
    }
</script>
@endpush
