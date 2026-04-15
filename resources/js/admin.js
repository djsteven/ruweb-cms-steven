import '../css/admin.css';
import { initEditorEngine } from './editor-engine.js';

const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    window.csrfToken = token;
}
const adminI18n = window.adminI18n || {};
const t = (key, fallback = '') => adminI18n[key] ?? fallback;
const maxUploadKb = Number(adminI18n.maxUploadKb || 0);
window.initEditorEngine = initEditorEngine;

const selectors = Array.from(document.querySelectorAll('.media-selector'));
const libraryModal = document.getElementById('media-library-modal');
const libraryClose = document.getElementById('media-library-close');
const libraryGrid = document.getElementById('media-library-grid');
const librarySearch = document.getElementById('media-library-search');
const libraryRefresh = document.getElementById('media-library-refresh');
const libraryStatus = document.getElementById('media-library-status');

let activeSelector = null;
let searchTimer = null;
const LIBRARY_PAGE_SIZE = 20;
const libraryItemsById = new Map();
const libraryState = {
    page: 1,
    hasMore: true,
    isLoading: false,
    requestId: 0,
    search: '',
    type: '',
};

// Upload modal
const uploadModal = document.getElementById('upload-modal');
const uploadClose = document.getElementById('upload-close');
const uploadForm = document.getElementById('upload-form');
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const uploadProgress = document.getElementById('upload-progress');
const fileNameLabel = document.getElementById('file-name');
const uploadEmptyState = document.getElementById('upload-empty-state');
const uploadPreviewWrap = document.getElementById('upload-preview-wrap');
const uploadPreviewImage = document.getElementById('upload-preview-image');
const uploadRemoveFile = document.getElementById('upload-remove-file');

let uploadFromLibrary = false;
let selectedUploadFiles = [];
let uploadPreviewUrl = null;

function clearSelectedUploadFiles() {
    selectedUploadFiles = [];
    if (fileInput) {
        fileInput.value = '';
    }
    if (fileNameLabel) {
        fileNameLabel.textContent = '';
    }
    if (uploadPreviewUrl) {
        URL.revokeObjectURL(uploadPreviewUrl);
        uploadPreviewUrl = null;
    }
    if (uploadPreviewImage) {
        uploadPreviewImage.src = '';
        uploadPreviewImage.alt = '';
    }
    uploadPreviewWrap?.classList.add('hidden');
    uploadEmptyState?.classList.remove('hidden');
    uploadRemoveFile?.classList.add('hidden');
}

function setSelectedUploadFiles(files) {
    if (!files?.length) {
        clearSelectedUploadFiles();
        return;
    }

    selectedUploadFiles = Array.from(files);
    const oversizedFile = maxUploadKb > 0
        ? selectedUploadFiles.find((file) => file.size > maxUploadKb * 1024)
        : null;

    if (fileNameLabel) {
        fileNameLabel.textContent = selectedUploadFiles.length === 1
            ? selectedUploadFiles[0].name
            : `${selectedUploadFiles.length} files selected`;
    }

    if (oversizedFile) {
        const maxMb = (maxUploadKb / 1024).toFixed(1);
        alert(`${t('validationFileMax', t('uploadFailed'))} (max ${maxMb} MB): ${oversizedFile.name}`);
        clearSelectedUploadFiles();
        return;
    }

    if (selectedUploadFiles.length !== 1) {
        if (uploadPreviewUrl) {
            URL.revokeObjectURL(uploadPreviewUrl);
            uploadPreviewUrl = null;
        }
        uploadPreviewWrap?.classList.add('hidden');
        uploadRemoveFile?.classList.remove('hidden');
        uploadEmptyState?.classList.remove('hidden');
        return;
    }

    const file = selectedUploadFiles[0];
    const isImage = String(file.type || '').startsWith('image/');
    if (!isImage) {
        if (uploadPreviewUrl) {
            URL.revokeObjectURL(uploadPreviewUrl);
            uploadPreviewUrl = null;
        }
        uploadPreviewWrap?.classList.add('hidden');
        uploadRemoveFile?.classList.remove('hidden');
        uploadEmptyState?.classList.remove('hidden');
        return;
    }

    if (uploadPreviewUrl) {
        URL.revokeObjectURL(uploadPreviewUrl);
    }
    uploadPreviewUrl = URL.createObjectURL(file);

    if (uploadPreviewImage) {
        uploadPreviewImage.src = uploadPreviewUrl;
        uploadPreviewImage.alt = file.name;
    }
    uploadEmptyState?.classList.add('hidden');
    uploadPreviewWrap?.classList.remove('hidden');
    uploadRemoveFile?.classList.remove('hidden');
}

function openUpload(fromLibrary = false) {
    uploadFromLibrary = fromLibrary;
    uploadModal?.classList.remove('hidden');
}

function closeUpload() {
    uploadModal?.classList.add('hidden');
    uploadForm?.reset();
    clearSelectedUploadFiles();
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
        setSelectedUploadFiles(e.dataTransfer.files);
    }
});
fileInput?.addEventListener('change', () => {
    setSelectedUploadFiles(fileInput.files || []);
});
uploadRemoveFile?.addEventListener('click', (event) => {
    event.preventDefault();
    event.stopPropagation();
    clearSelectedUploadFiles();
});

uploadForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!selectedUploadFiles.length) {
        alert(t('chooseFile', t('uploadFailed')));
        return;
    }

    const formData = new FormData(uploadForm);
    formData.delete('files[]');
    selectedUploadFiles.forEach((file) => {
        formData.append('files[]', file);
    });

    uploadProgress?.classList.remove('hidden');
    try {
        const response = await fetch('/admin/media', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });
        if (response.ok) {
            const payload = await response.json();
            const uploadedItems = Array.isArray(payload?.data)
                ? payload.data
                : (payload ? [payload] : []);

            if (uploadFromLibrary && activeSelector) {
                closeUpload();

                if (uploadedItems.length === 1) {
                    renderSelectorPreview(activeSelector, uploadedItems[0]);
                    notifySelectorChange(activeSelector);
                    closeLibrary();
                } else {
                    void loadLibrary({ reset: true });
                }
            } else {
                window.location.reload();
            }
        } else {
            const contentType = response.headers.get('content-type') || '';
            const data = contentType.includes('application/json') ? await response.json() : null;
            const validationError = data?.errors ? Object.values(data.errors).flat()[0] : null;
            alert(validationError || data?.error || data?.message || t('uploadFailed'));
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
    loadLibrary({ reset: true });
}

function closeLibrary() {
    activeSelector = null;

    if (!libraryModal) {
        return;
    }

    libraryModal.classList.add('hidden');
}

function renderLibraryItems(items, { append = false } = {}) {
    if (!libraryGrid) {
        return;
    }

    if (!append && !items.length) {
        libraryItemsById.clear();
        libraryGrid.innerHTML = `<div class="col-span-full text-sm text-gray-500 py-6 text-center">${escapeHtml(t('noMediaFound'))}</div>`;
        return;
    }

    const html = items.map((item) => {
        libraryItemsById.set(String(item.id), item);
        const isImage = String(item.mime_type || '').startsWith('image/');

        return `
            <button type="button" class="media-library-item text-left bg-[#111111] border border-white/10 rounded-lg overflow-hidden hover:border-emerald-500/50 transition-colors" data-id="${item.id}">
                <div class="aspect-square bg-[#1a1a1a] flex items-center justify-center overflow-hidden">
                    ${isImage
                        ? `<img src="${escapeHtml(item.url)}" alt="${escapeHtml(item.alt || item.title || item.original_filename)}" class="w-full h-full object-contain">`
                        : `<svg class="w-10 h-10 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>`}
                </div>
                <div class="p-3">
                    <p class="text-sm text-white truncate">${escapeHtml(item.title || item.original_filename)}</p>
                    <p class="text-xs text-gray-500 truncate">${escapeHtml(item.original_filename || '')}</p>
                </div>
            </button>
        `;
    }).join('');

    if (append) {
        libraryGrid.insertAdjacentHTML('beforeend', html);
        return;
    }

    libraryGrid.innerHTML = html;
}

function currentLibraryFilters() {
    return {
        search: librarySearch?.value?.trim() || '',
        type: activeSelector?.dataset?.type || '',
    };
}

async function loadLibrary({ reset = false } = {}) {
    if (!libraryGrid || !libraryStatus) {
        return;
    }

    if (libraryState.isLoading && !reset) {
        return;
    }

    if (!reset && !libraryState.hasMore) {
        return;
    }

    if (reset) {
        libraryState.requestId += 1;
        libraryState.isLoading = false;
        const filters = currentLibraryFilters();
        libraryState.page = 1;
        libraryState.hasMore = true;
        libraryState.search = filters.search;
        libraryState.type = filters.type;
        libraryItemsById.clear();
        libraryGrid.innerHTML = '';
    }

    const requestId = ++libraryState.requestId;
    const page = libraryState.page;

    libraryState.isLoading = true;
    libraryStatus.textContent = t('loadingMedia');
    libraryStatus.classList.remove('hidden');

    const params = new URLSearchParams();
    if (libraryState.search) {
        params.set('search', libraryState.search);
    }
    if (libraryState.type) {
        params.set('type', libraryState.type);
    }
    params.set('page', String(page));
    params.set('per_page', String(LIBRARY_PAGE_SIZE));

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
        if (requestId !== libraryState.requestId) {
            return;
        }

        const items = payload.data || [];
        renderLibraryItems(items, { append: page > 1 });
        libraryState.page = Number(payload.current_page || page) + 1;
        libraryState.hasMore = Boolean(payload.next_page_url);
        libraryStatus.classList.add('hidden');

        if (!libraryGrid.querySelector('.media-library-item') && !libraryState.hasMore) {
            renderLibraryItems([], { append: false });
            return;
        }

        if (libraryState.hasMore && libraryGrid.scrollHeight <= libraryGrid.clientHeight) {
            void loadLibrary();
        }
    } catch (_) {
        if (requestId === libraryState.requestId) {
            libraryStatus.textContent = t('unableToLoadMediaLibrary');
            libraryStatus.classList.remove('hidden');
        }
    } finally {
        if (requestId === libraryState.requestId) {
            libraryState.isLoading = false;
        }
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

libraryRefresh?.addEventListener('click', () => loadLibrary({ reset: true }));

librarySearch?.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadLibrary({ reset: true }), 250);
});

libraryGrid?.addEventListener('scroll', () => {
    if (libraryState.isLoading || !libraryState.hasMore) {
        return;
    }

    const threshold = 200;
    const distanceToBottom = libraryGrid.scrollHeight - libraryGrid.scrollTop - libraryGrid.clientHeight;
    if (distanceToBottom <= threshold) {
        void loadLibrary();
    }
});

libraryGrid?.addEventListener('click', (event) => {
    const button = event.target.closest('.media-library-item');
    if (!button || !activeSelector) {
        return;
    }

    const selected = libraryItemsById.get(String(button.dataset.id));
    if (!selected) {
        return;
    }

    const selector = activeSelector;
    renderSelectorPreview(selector, selected);
    notifySelectorChange(selector);
    closeLibrary();
});
