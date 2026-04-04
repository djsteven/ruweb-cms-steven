@extends('admin.layouts.editor')

@section('editor-title', $page->title)

@section('editor-actions')
    @if ($page->isPublished())
        <a href="{{ $page->url() }}" target="_blank"
           class="text-xs text-gray-500 hover:text-gray-300 transition-colors hidden sm:inline">
            {{ __('admin.view_live') }}
        </a>
    @endif
@endsection

@section('editor-form')
    @include('admin.pages._form', ['page' => $page, 'templates' => $templates])
@endsection

@section('editor-footer')
    <div class="flex-none px-5 py-3 border-t border-white/[0.06] bg-[#111111]">
        @if ($page->isPublished())
            <button type="button" id="update-btn" disabled
                    class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-medium rounded-md transition-colors">
                {{ __('admin.btn_save_changes') }}
            </button>
        @else
            <div class="flex gap-2">
                <button type="button" id="save-draft-btn" disabled
                        class="flex-1 px-3 py-2 bg-white/5 hover:bg-white/10 disabled:opacity-40 disabled:cursor-not-allowed text-gray-300 text-sm font-medium rounded-md transition-colors">
                    {{ __('admin.btn_save_draft') }}
                </button>
                <button type="button" id="publish-btn"
                        class="flex-1 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
                    {{ __('admin.btn_publish') }}
                </button>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('editor-form');
    const iframe = document.getElementById('preview-frame');
    const previewUrl = '{{ route('admin.pages.preview', $page) }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const headerSaveBtn = document.getElementById('save-btn');
    const updateBtn = document.getElementById('update-btn');
    const saveDraftBtn = document.getElementById('save-draft-btn');
    const publishBtn = document.getElementById('publish-btn');
    const DRAFT_KEY = 'page-draft-{{ $page->id }}';

    let previewTimer;
    let savedSnapshot = formSnapshot();

    // --- Snapshot / dirty detection ---

    function formSnapshot() {
        const data = new FormData(form);
        data.delete('_token');
        data.delete('_method');
        const entries = [];
        for (const [k, v] of data.entries()) entries.push(k + '=' + v);
        return entries.join('&');
    }

    function isDirty() {
        return formSnapshot() !== savedSnapshot;
    }

    function updateDirtyState() {
        const dirty = isDirty();
        if (updateBtn) updateBtn.disabled = !dirty;
        if (saveDraftBtn) saveDraftBtn.disabled = !dirty;
        headerSaveBtn.disabled = !dirty;
    }

    // --- localStorage draft ---

    function saveDraft() {
        const data = {};
        new FormData(form).forEach((v, k) => {
            if (k === '_token' || k === '_method') return;
            data[k] = v;
        });
        localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
    }

    function restoreDraft() {
        const raw = localStorage.getItem(DRAFT_KEY);
        if (!raw) return;

        try {
            const data = JSON.parse(raw);
            for (const [k, v] of Object.entries(data)) {
                const el = form.querySelector('[name="' + CSS.escape(k) + '"]');
                if (!el) continue;
                if (el.tagName === 'SELECT' || el.tagName === 'TEXTAREA') {
                    el.value = v;
                } else if (el.type !== 'hidden') {
                    el.value = v;
                }
            }
        } catch (_) {}
    }

    function clearDraft() {
        localStorage.removeItem(DRAFT_KEY);
    }

    // --- Preview ---

    function refreshPreview() {
        const previewData = new FormData(form);
        previewData.delete('_method');
        fetch(previewUrl, {
            method: 'POST',
            body: previewData,
            headers: { 'X-CSRF-TOKEN': csrfToken },
        }).then(res => {
            if (res.ok) return res.text().then(html => { iframe.srcdoc = html; });
        }).catch(() => {});
    }

    // --- Save ---

    async function save(statusOverride) {
        if (updateBtn) updateBtn.disabled = true;
        if (saveDraftBtn) saveDraftBtn.disabled = true;
        headerSaveBtn.disabled = true;

        if (statusOverride) {
            const statusSelect = form.querySelector('[name="status"]');
            if (statusSelect) statusSelect.value = statusOverride;
        }

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });
            if (res.ok) {
                savedSnapshot = formSnapshot();
                clearDraft();
                showToast('{{ __('admin.saved_success') }}');
                updateDirtyState();
            } else {
                showToast('{{ __('admin.save_error') }}', 'error');
                updateDirtyState();
            }
        } catch (_) {
            showToast('{{ __('admin.save_error') }}', 'error');
            updateDirtyState();
        }
    }

    // --- Events ---

    function onFormChange() {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(refreshPreview, 600);
        saveDraft();
        updateDirtyState();
    }

    form.addEventListener('input', onFormChange);
    form.addEventListener('change', onFormChange);

    if (updateBtn) updateBtn.addEventListener('click', () => save());
    if (saveDraftBtn) saveDraftBtn.addEventListener('click', () => save('draft'));
    if (publishBtn) publishBtn.addEventListener('click', () => save('published'));
    headerSaveBtn.addEventListener('click', () => save());

    restoreDraft();
    updateDirtyState();
    refreshPreview();
})();
</script>
@endpush
