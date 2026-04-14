import '../css/admin.css';

const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    window.csrfToken = token;
}
const adminI18n = window.adminI18n || {};
const t = (key, fallback = '') => adminI18n[key] ?? fallback;

const selectors = Array.from(document.querySelectorAll('.media-selector'));
const libraryModal = document.getElementById('media-library-modal');
const libraryClose = document.getElementById('media-library-close');
const libraryGrid = document.getElementById('media-library-grid');
const librarySearch = document.getElementById('media-library-search');
const libraryRefresh = document.getElementById('media-library-refresh');
const libraryStatus = document.getElementById('media-library-status');

let activeSelector = null;
let searchTimer = null;

// Upload modal
const uploadModal = document.getElementById('upload-modal');
const uploadClose = document.getElementById('upload-close');
const uploadForm = document.getElementById('upload-form');
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const uploadProgress = document.getElementById('upload-progress');
const fileNameLabel = document.getElementById('file-name');

let uploadFromLibrary = false;

function openUpload(fromLibrary = false) {
    uploadFromLibrary = fromLibrary;
    uploadModal?.classList.remove('hidden');
}

function closeUpload() {
    uploadModal?.classList.add('hidden');
    uploadForm?.reset();
    if (fileNameLabel) fileNameLabel.textContent = '';
}

uploadClose?.addEventListener('click', closeUpload);
dropZone?.addEventListener('click', () => fileInput?.click());
dropZone?.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-emerald-500/50');
});
dropZone?.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-emerald-500/50');
});
dropZone?.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-emerald-500/50');
    if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        if (fileNameLabel) fileNameLabel.textContent = e.dataTransfer.files[0].name;
    }
});
fileInput?.addEventListener('change', () => {
    if (fileInput.files.length && fileNameLabel) fileNameLabel.textContent = fileInput.files[0].name;
});

uploadForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    uploadProgress?.classList.remove('hidden');
    try {
        const response = await fetch('/admin/media', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.csrfToken },
            body: new FormData(uploadForm),
        });
        if (response.ok) {
            const newMedia = await response.json();
            if (uploadFromLibrary && activeSelector) {
                renderSelectorPreview(activeSelector, newMedia);
                notifySelectorChange(activeSelector);
                closeUpload();
                closeLibrary();
            } else {
                window.location.reload();
            }
        } else {
            const data = await response.json();
            alert(data.error || data.message || t('uploadFailed'));
        }
    } catch {
        alert(t('uploadFailed'));
    } finally {
        uploadProgress?.classList.add('hidden');
    }
});

document.getElementById('upload-btn')?.addEventListener('click', () => openUpload(false));
document.getElementById('upload-btn-empty')?.addEventListener('click', () => openUpload(false));
document.getElementById('media-library-upload')?.addEventListener('click', () => openUpload(true));

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function selectorNodes(selector) {
    return {
        input: selector.querySelector('.media-selector-input'),
        preview: selector.querySelector('.media-selector-preview'),
        clear: selector.querySelector('.media-selector-clear'),
        button: selector.querySelector('.media-selector-btn'),
    };
}

function notifySelectorChange(selector) {
    const { input } = selectorNodes(selector);

    if (!input) {
        return;
    }

    input.dispatchEvent(new Event('input', { bubbles: true }));
    input.dispatchEvent(new Event('change', { bubbles: true }));
}

function renderSelectorPreview(selector, media) {
    const { input, preview, clear, button } = selectorNodes(selector);
    const defaultLabel = button?.dataset.label || button?.textContent || t('chooseFile');

    if (!preview || !input || !clear || !button) {
        return;
    }

    if (!media) {
        preview.innerHTML = '';
        input.value = '';
        clear.classList.add('hidden');
        button.textContent = defaultLabel;
        return;
    }

    input.value = media.id;
    clear.classList.remove('hidden');
    button.textContent = defaultLabel;

    const isImage = String(media.mime_type || '').startsWith('image/');
    preview.innerHTML = `
        <div class="flex items-center gap-3 rounded-lg border border-white/10 bg-[#141414] p-3">
            <div class="w-16 h-16 rounded-md overflow-hidden bg-[#1a1a1a] flex items-center justify-center shrink-0">
                ${isImage
                    ? `<img src="${escapeHtml(media.url)}" alt="${escapeHtml(media.alt || media.title || media.original_filename)}" class="w-full h-full object-cover">`
                    : `<svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>`}
            </div>
            <div class="min-w-0">
                <p class="text-sm text-white truncate">${escapeHtml(media.title || media.original_filename)}</p>
                <p class="text-xs text-gray-500 truncate">${escapeHtml(media.original_filename || '')}</p>
            </div>
        </div>
    `;
}

async function fetchMediaItem(id) {
    const response = await fetch(`/admin/media/${id}`, {
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    if (!response.ok) {
        throw new Error(t('unableToLoadMediaItem'));
    }

    return response.json();
}

function openLibrary(selector) {
    activeSelector = selector;

    if (!libraryModal) {
        return;
    }

    libraryModal.classList.remove('hidden');
    loadLibrary(librarySearch?.value || '');
}

function closeLibrary() {
    activeSelector = null;

    if (!libraryModal) {
        return;
    }

    libraryModal.classList.add('hidden');
}

function renderLibraryItems(items) {
    if (!libraryGrid) {
        return;
    }

    if (!items.length) {
        libraryGrid.innerHTML = `<div class="col-span-full text-sm text-gray-500 py-6 text-center">${escapeHtml(t('noMediaFound'))}</div>`;
        return;
    }

    libraryGrid.innerHTML = items.map((item) => {
        const isImage = String(item.mime_type || '').startsWith('image/');

        return `
            <button type="button" class="media-library-item text-left bg-[#111111] border border-white/10 rounded-lg overflow-hidden hover:border-emerald-500/50 transition-colors" data-id="${item.id}">
                <div class="aspect-square bg-[#1a1a1a] flex items-center justify-center overflow-hidden">
                    ${isImage
                        ? `<img src="${escapeHtml(item.url)}" alt="${escapeHtml(item.alt || item.title || item.original_filename)}" class="w-full h-full object-cover">`
                        : `<svg class="w-10 h-10 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>`}
                </div>
                <div class="p-3">
                    <p class="text-sm text-white truncate">${escapeHtml(item.title || item.original_filename)}</p>
                    <p class="text-xs text-gray-500 truncate">${escapeHtml(item.original_filename || '')}</p>
                </div>
            </button>
        `;
    }).join('');

    libraryGrid.querySelectorAll('.media-library-item').forEach((button) => {
        button.addEventListener('click', async () => {
            if (!activeSelector) {
                return;
            }

            const selected = items.find((item) => String(item.id) === button.dataset.id);
            const selector = activeSelector;

            renderSelectorPreview(selector, selected);
            notifySelectorChange(selector);
            closeLibrary();
        });
    });
}

async function loadLibrary(search = '') {
    if (!libraryGrid || !libraryStatus) {
        return;
    }

    libraryStatus.textContent = t('loadingMedia');
    libraryStatus.classList.remove('hidden');
    libraryGrid.innerHTML = '';

    const params = new URLSearchParams();
    if (search) {
        params.set('search', search);
    }

    try {
        const response = await fetch(`/admin/media?${params.toString()}`, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error(t('unableToLoadMediaLibrary'));
        }

        const payload = await response.json();
        renderLibraryItems(payload.data || []);
        libraryStatus.classList.add('hidden');
    } catch (_) {
        libraryStatus.textContent = t('unableToLoadMediaLibrary');
        libraryStatus.classList.remove('hidden');
    }
}

selectors.forEach((selector) => {
    const { input, clear, button } = selectorNodes(selector);

    button?.addEventListener('click', () => openLibrary(selector));

    clear?.addEventListener('click', () => {
        renderSelectorPreview(selector, null);
        notifySelectorChange(selector);
    });

    if (input?.value) {
        fetchMediaItem(input.value)
            .then((media) => renderSelectorPreview(selector, media))
            .catch(() => renderSelectorPreview(selector, null));
    }
});

libraryClose?.addEventListener('click', closeLibrary);

libraryModal?.addEventListener('click', (event) => {
    if (event.target === libraryModal) {
        closeLibrary();
    }
});

libraryRefresh?.addEventListener('click', () => loadLibrary(librarySearch?.value || ''));

librarySearch?.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadLibrary(librarySearch.value || ''), 250);
});
