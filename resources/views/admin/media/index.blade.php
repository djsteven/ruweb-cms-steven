@extends('admin.layouts.app')

@section('title', __('admin.media'))

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
    <h1 class="text-lg font-semibold text-white">{{ __('admin.media') }}</h1>

    <button id="upload-btn" class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('admin.upload') }}
    </button>
</div>

<!-- Filters -->
<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <form id="filter-form" method="GET" class="flex flex-1 gap-2">
        <select id="filter-type" name="type" class="bg-[#1a1a1a] border border-white/10 text-gray-400 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50">
            <option value="">{{ __('admin.all_types') }}</option>
            <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>{{ __('admin.images') }}</option>
            <option value="application" {{ request('type') === 'application' ? 'selected' : '' }}>{{ __('admin.documents') }}</option>
        </select>
        <input
            id="filter-search"
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="{{ __('admin.search_media_ph') }}"
            class="flex-1 bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
        >
    </form>
</div>

<!-- Media Grid -->
@if ($media->isEmpty())
    <div class="text-center py-20 text-gray-600">
        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm font-medium text-gray-500">{{ __('admin.no_media_yet') }}</p>
        <p class="text-sm text-gray-600 mt-1">{{ __('admin.no_media_hint') }}</p>
        <button id="upload-btn-empty" class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
            {{ __('admin.upload_file') }}
        </button>
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach ($media as $item)
            <div class="media-item relative bg-[#141414] ring-1 ring-white/[0.06] rounded-lg overflow-hidden cursor-pointer hover:ring-white/20 transition-all"
                 data-id="{{ $item->id }}"
                 data-url="{{ $item->url() }}"
                 data-title="{{ $item->title }}"
                 data-alt="{{ $item->alt }}"
                 data-size="{{ $item->formattedSize() }}"
                 data-mime="{{ $item->mime_type }}"
                 data-filename="{{ $item->original_filename }}">
                <div class="aspect-square flex items-center justify-center bg-[#1a1a1a]">
                    @if ($item->isImage())
                        <img src="{{ $item->url() }}" alt="{{ $item->alt }}" class="w-full h-full object-cover">
                    @else
                        <svg class="w-10 h-10 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    @endif
                </div>
                <div class="px-2 py-1.5">
                    <p class="text-xs text-gray-500 truncate">{{ $item->original_filename }}</p>
                    <p class="text-xs text-gray-700">{{ $item->formattedSize() }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $media->withQueryString()->links() }}
    </div>
@endif

@include('admin.media._detail-panel')

@push('scripts')
<script>
    // Reactive filters
    const filterType = document.getElementById('filter-type');
    const filterSearch = document.getElementById('filter-search');

    const submitFilter = (keepFocus = false) => {
        const params = new URLSearchParams();
        if (filterType.value) params.set('type', filterType.value);
        if (filterSearch.value) params.set('search', filterSearch.value);
        if (keepFocus) params.set('focus', 'search');
        window.location.href = '?' + params.toString();
    };

    filterType.addEventListener('change', () => submitFilter(false));

    let debounceTimer;
    filterSearch.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => submitFilter(true), 400);
    });

    if (new URLSearchParams(window.location.search).get('focus') === 'search') {
        filterSearch.focus();
        filterSearch.setSelectionRange(filterSearch.value.length, filterSearch.value.length);
    }

    // Detail panel
    const detailPanel = document.getElementById('detail-panel');
    const detailClose = document.getElementById('detail-close');
    let activeItem = null;

    document.querySelectorAll('.media-item').forEach(item => {
        item.addEventListener('click', () => {
            if (activeItem) activeItem.classList.remove('ring-2', 'ring-emerald-500/60');
            activeItem = item;
            item.classList.add('ring-2', 'ring-emerald-500/60');

            document.getElementById('detail-preview').innerHTML = item.dataset.mime.startsWith('image/')
                ? `<img src="${item.dataset.url}" alt="${item.dataset.alt}" class="max-w-full max-h-56 rounded object-contain">`
                : `<div class="text-gray-700 text-center py-8"><svg class="w-14 h-14 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>`;

            document.getElementById('detail-filename').textContent = item.dataset.filename;
            document.getElementById('detail-size').textContent = item.dataset.size;
            document.getElementById('detail-alt').value = item.dataset.alt || '';
            document.getElementById('detail-title').value = item.dataset.title || '';
            detailPanel.dataset.mediaId = item.dataset.id;
            detailPanel.classList.remove('hidden');
        });
    });

    detailClose?.addEventListener('click', () => {
        if (activeItem) activeItem.classList.remove('ring-2', 'ring-emerald-500/60');
        activeItem = null;
        detailPanel.classList.add('hidden');
    });

    document.getElementById('detail-save')?.addEventListener('click', async () => {
        const id = detailPanel.dataset.mediaId;
        const response = await fetch(`/admin/media/${id}`, {
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                alt: document.getElementById('detail-alt').value,
                title: document.getElementById('detail-title').value,
            }),
        });
        if (response.ok) window.location.reload();
    });

    document.getElementById('detail-delete')?.addEventListener('click', async () => {
        if (!confirm('{{ __('admin.confirm_delete_media') }}')) return;
        const id = detailPanel.dataset.mediaId;
        const response = await fetch(`/admin/media/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': window.csrfToken },
        });
        if (response.ok) window.location.reload();
    });
</script>
@endpush
@endsection
