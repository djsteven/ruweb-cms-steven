export function initEditorEngine(config = {}) {
    const form = document.getElementById('editor-form');
    const iframe = document.getElementById('preview-frame');
    const headerSaveBtn = document.getElementById('save-btn');
    const updateBtn = document.getElementById('update-btn');
    const saveDraftBtn = document.getElementById('save-draft-btn');
    const publishBtn = document.getElementById('publish-btn');

    if (!form || !headerSaveBtn) {
        return;
    }

    const previewUrl = config.previewUrl || null;
    const savedMsg = config.savedMsg || 'Saved';
    const errorMsg = config.errorMsg || 'Save error';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let previewTimer = null;
    let savedSnapshot = formSnapshot();
    let savedStatus = currentStatus();

    function formSnapshot() {
        const data = new FormData(form);
        data.delete('_token');
        data.delete('_method');

        const entries = [];
        for (const [key, value] of data.entries()) {
            entries.push(`${key}=${value}`);
        }

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

    function currentStatus() {
        return new FormData(form).get('status') || null;
    }

    function refreshPreview() {
        if (!previewUrl || !iframe) {
            return;
        }

        const previewData = new FormData(form);
        previewData.delete('_method');

        let previousScrollY = 0;
        try {
            previousScrollY = iframe.contentWindow?.scrollY || 0;
        } catch (_) {}

        fetch(previewUrl, {
            method: 'POST',
            body: previewData,
            headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
        })
            .then((res) => {
                if (!res.ok) {
                    return null;
                }

                return res.text();
            })
            .then((html) => {
                if (typeof html !== 'string') {
                    return;
                }

                const restoreScroll = () => {
                    try {
                        iframe.contentWindow?.scrollTo(0, previousScrollY);
                    } catch (_) {}
                };

                iframe.addEventListener('load', restoreScroll, { once: true });
                iframe.srcdoc = html;
            })
            .catch(() => {});
    }

    async function save(statusOverride) {
        const beforeSave = new CustomEvent('editor:before-save', { cancelable: true });
        form.dispatchEvent(beforeSave);

        if (beforeSave.defaultPrevented) {
            updateDirtyState();
            return;
        }

        if (updateBtn) updateBtn.disabled = true;
        if (saveDraftBtn) saveDraftBtn.disabled = true;
        headerSaveBtn.disabled = true;

        try {
            const requestData = new FormData(form);
            if (statusOverride) {
                requestData.set('status', statusOverride);
            }

            const response = await fetch(form.action, {
                method: 'POST',
                body: requestData,
                headers: {
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                    Accept: 'application/json',
                },
            });

            if (response.ok) {
                const nextStatus = requestData.get('status') || null;
                savedSnapshot = formSnapshot();
                showToast(savedMsg);

                if (nextStatus && savedStatus && nextStatus !== savedStatus) {
                    window.removeEventListener('beforeunload', onBeforeUnload);
                    window.location.reload();
                    return;
                }

                savedStatus = nextStatus || savedStatus;
            } else {
                const message = await responseErrorMessage(response);
                showToast(message || errorMsg, 'error');
            }
        } catch (_) {
            showToast(errorMsg, 'error');
        } finally {
            updateDirtyState();
        }
    }

    async function responseErrorMessage(response) {
        const contentType = response.headers.get('content-type') || '';

        if (!contentType.includes('application/json')) {
            return null;
        }

        try {
            const payload = await response.json();
            const firstError = payload?.errors
                ? Object.values(payload.errors).flat()[0]
                : null;

            return firstError || payload?.message || null;
        } catch (_) {
            return null;
        }
    }

    function onFormChange() {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(refreshPreview, 600);
        updateDirtyState();
    }

    function onBeforeUnload(event) {
        if (!isDirty()) return;
        event.preventDefault();
        event.returnValue = 'unsaved';
        return 'unsaved';
    }

    form.addEventListener('input', onFormChange);
    form.addEventListener('change', onFormChange);
    window.addEventListener('beforeunload', onBeforeUnload);

    if (updateBtn) updateBtn.addEventListener('click', () => save());
    if (saveDraftBtn) saveDraftBtn.addEventListener('click', () => save('draft'));
    if (publishBtn) publishBtn.addEventListener('click', () => save('published'));
    headerSaveBtn.addEventListener('click', () => save());

    updateDirtyState();
    refreshPreview();
}
