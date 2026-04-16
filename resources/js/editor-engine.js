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
    const draftKey = config.draftKey || null;
    const savedMsg = config.savedMsg || 'Saved';
    const errorMsg = config.errorMsg || 'Save error';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let previewTimer = null;
    let savedSnapshot = formSnapshot();

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

    function updateDirtyState() {
        const dirty = formSnapshot() !== savedSnapshot;
        if (updateBtn) updateBtn.disabled = !dirty;
        if (saveDraftBtn) saveDraftBtn.disabled = !dirty;
        headerSaveBtn.disabled = !dirty;
    }

    function createDraftPayload() {
        const data = {};
        const formData = new FormData(form);
        formData.delete('_token');
        formData.delete('_method');

        for (const [key, value] of formData.entries()) {
            if (Object.hasOwn(data, key)) {
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }

        return data;
    }

    function saveDraftToStorage() {
        if (!draftKey) {
            return;
        }

        try {
            localStorage.setItem(draftKey, JSON.stringify(createDraftPayload()));
        } catch (_) {}
    }

    function clearDraftStorage() {
        if (!draftKey) {
            return;
        }

        localStorage.removeItem(draftKey);
    }

    function restoreDraftFromStorage() {
        if (!draftKey) {
            return;
        }

        const raw = localStorage.getItem(draftKey);
        if (!raw) {
            return;
        }

        try {
            const data = JSON.parse(raw);
            for (const [key, rawValue] of Object.entries(data)) {
                const values = Array.isArray(rawValue) ? rawValue.map(String) : [String(rawValue)];
                const elements = form.querySelectorAll(`[name="${CSS.escape(key)}"]`);

                if (!elements.length) {
                    continue;
                }

                elements.forEach((element) => {
                    const type = element.type || '';
                    if (type === 'checkbox' || type === 'radio') {
                        element.checked = values.includes(String(element.value));
                        return;
                    }

                    if (element.tagName === 'SELECT' && element.multiple) {
                        Array.from(element.options).forEach((option) => {
                            option.selected = values.includes(String(option.value));
                        });
                        return;
                    }

                    if (type !== 'hidden') {
                        element.value = values[0] ?? '';
                    }
                });
            }
        } catch (_) {}
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
                savedSnapshot = formSnapshot();
                clearDraftStorage();
                showToast(savedMsg);
            } else {
                showToast(errorMsg, 'error');
            }
        } catch (_) {
            showToast(errorMsg, 'error');
        } finally {
            updateDirtyState();
        }
    }

    function onFormChange() {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(refreshPreview, 600);
        saveDraftToStorage();
        updateDirtyState();
    }

    form.addEventListener('input', onFormChange);
    form.addEventListener('change', onFormChange);

    if (updateBtn) updateBtn.addEventListener('click', () => save());
    if (saveDraftBtn) saveDraftBtn.addEventListener('click', () => save('draft'));
    if (publishBtn) publishBtn.addEventListener('click', () => save('published'));
    headerSaveBtn.addEventListener('click', () => save());

    restoreDraftFromStorage();
    updateDirtyState();
    refreshPreview();
}
