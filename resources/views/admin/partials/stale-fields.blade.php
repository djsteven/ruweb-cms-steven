@php
    $staleFieldNames = $staleFieldNames ?? [];
@endphp

@if(! empty($staleFieldNames))
    <div id="stale-fields-alert" class="hidden mb-4 rounded-md border border-amber-400/30 bg-amber-500/10 px-3 py-2 text-xs text-amber-200">
        {{ __('admin.save_blocked_outdated') }}
    </div>

    @push('scripts')
    <script>
        (() => {
            const staleFieldNames = @json(array_values($staleFieldNames));
            const form = document.getElementById('editor-form');
            const alertBox = document.getElementById('stale-fields-alert');

            if (!form || staleFieldNames.length === 0) return;

            const initialValues = new Map();
            const acknowledged = new Set();
            const fieldsByName = new Map();

            const fieldValue = field => {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    return field.checked ? field.value : '';
                }

                return field.value ?? '';
            };

            const findField = name => Array.from(form.querySelectorAll('[name]')).find(field => field.name === name);

            const addHiddenAcknowledgement = name => {
                const exists = Array.from(form.querySelectorAll('input[type="hidden"][name="acknowledged_fields[]"]'))
                    .some(input => input.value === name);

                if (exists) {
                    return;
                }

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'acknowledged_fields[]';
                input.value = name;
                form.appendChild(input);
            };

            const isPending = name => {
                const field = fieldsByName.get(name);

                return field && !acknowledged.has(name) && fieldValue(field) === initialValues.get(name);
            };

            const updateSectionBadge = section => {
                if (!section) return;

                const pendingCount = Array.from(section.querySelectorAll('[name]'))
                    .filter(field => staleFieldNames.includes(field.name) && isPending(field.name))
                    .length;
                let badge = section.querySelector('[data-stale-section-badge]');

                if (pendingCount === 0) {
                    badge?.remove();
                    return;
                }

                if (!badge) {
                    const title = section.querySelector('[data-accordion-toggle] h3');
                    badge = document.createElement('span');
                    badge.dataset.staleSectionBadge = 'true';
                    badge.className = 'inline-flex h-5 min-w-5 items-center justify-center gap-1 rounded-full border border-amber-400/30 bg-amber-400/10 px-1.5 text-[11px] font-medium text-amber-200';
                    badge.title = '{{ __('admin.field_outdated') }}';
                    badge.innerHTML = `
                        <svg aria-hidden="true" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"></path>
                            <path d="M12 9v4"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                        <span data-stale-section-count></span>
                    `;
                    title?.after(badge);
                }

                const count = badge.querySelector('[data-stale-section-count]');
                if (count) count.textContent = pendingCount > 1 ? String(pendingCount) : '';
            };

            const updatePanelBadge = panel => {
                if (!panel?.dataset?.panel) return;

                const pendingCount = Array.from(panel.querySelectorAll('[name]'))
                    .filter(field => staleFieldNames.includes(field.name) && isPending(field.name))
                    .length;
                const tab = document.querySelector(`.form-tab[data-tab="${panel.dataset.panel}"]`);
                let badge = tab?.querySelector('[data-stale-tab-badge]');

                if (!tab) return;

                if (pendingCount === 0) {
                    badge?.remove();
                    return;
                }

                if (!badge) {
                    badge = document.createElement('span');
                    badge.dataset.staleTabBadge = 'true';
                    badge.className = 'ml-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-amber-400/15 px-1 text-[10px] font-medium text-amber-200 align-middle';
                    badge.title = '{{ __('admin.field_outdated') }}';
                    tab.appendChild(badge);
                }

                badge.textContent = pendingCount > 1 ? String(pendingCount) : '!';
            };

            const updateAllBadges = () => {
                document.querySelectorAll('.template-section').forEach(updateSectionBadge);
                document.querySelectorAll('.form-panel').forEach(updatePanelBadge);
            };

            const revealField = field => {
                const panel = field.closest('.form-panel');
                const tab = panel?.dataset?.panel
                    ? document.querySelector(`.form-tab[data-tab="${panel.dataset.panel}"]`)
                    : null;

                if (tab && panel?.classList.contains('hidden')) {
                    tab.click();
                }

                const section = field.closest('.template-section');
                const body = section?.querySelector('[data-accordion-body]');
                const toggle = section?.querySelector('[data-accordion-toggle]');
                const icon = section?.querySelector('[data-accordion-icon]');

                if (body?.classList.contains('hidden')) {
                    body.classList.remove('hidden');
                    icon?.classList.add('rotate-180');
                    toggle?.setAttribute('aria-expanded', 'true');
                }
            };

            const updateFieldState = (field, wrapper, name) => {
                const pending = isPending(name);

                field.classList.toggle('border-amber-400', pending);
                field.classList.toggle('ring-1', pending);
                field.classList.toggle('ring-amber-400/40', pending);
                field.classList.toggle('border-white/10', !pending);

                wrapper?.querySelector('[data-stale-hint]')?.classList.toggle('hidden', !pending);
                updateSectionBadge(field.closest('.template-section'));
                updatePanelBadge(field.closest('.form-panel'));
            };

            const clearFieldState = (field, wrapper, name) => {
                acknowledged.add(name);
                addHiddenAcknowledgement(name);
                updateFieldState(field, wrapper, name);
            };

            staleFieldNames.forEach(name => {
                const field = findField(name);
                if (!field) return;

                const wrapper = field.closest('div');
                fieldsByName.set(name, field);
                initialValues.set(name, fieldValue(field));

                const hint = document.createElement('div');
                hint.dataset.staleHint = 'true';
                hint.className = 'mt-1.5 flex flex-wrap items-center gap-2 text-[11px] leading-4 text-amber-200/80';
                hint.innerHTML = `
                    <span class="inline-flex items-center gap-1 text-amber-200">
                        <svg aria-hidden="true" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"></path>
                            <path d="M12 9v4"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                        {{ __('admin.field_outdated') }}
                    </span>
                    <span>{{ __('admin.field_outdated_hint') }}</span>
                    <button type="button" class="inline-flex items-center gap-1 rounded border border-amber-300/25 px-1.5 py-0.5 text-[11px] text-amber-100 hover:bg-amber-300/10">
                        <svg aria-hidden="true" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5"></path>
                        </svg>
                        {{ __('admin.mark_no_changes') }}
                    </button>
                `;

                hint.querySelector('button').addEventListener('click', () => clearFieldState(field, wrapper, name));
                wrapper?.appendChild(hint);
                updateFieldState(field, wrapper, name);

                const refreshVisualState = () => {
                    updateFieldState(field, wrapper, name);
                };

                field.addEventListener('input', refreshVisualState);
                field.addEventListener('change', refreshVisualState);
            });

            updateAllBadges();

            form.addEventListener('submit', event => {
                const blocked = staleFieldNames
                    .map(name => [name, findField(name)])
                    .find(([name, field]) => field && !acknowledged.has(name) && fieldValue(field) === initialValues.get(name));

                if (!blocked) return;

                event.preventDefault();
                alertBox?.classList.remove('hidden');
                revealField(blocked[1]);
                blocked[1].focus();
            });

            form.addEventListener('editor:before-save', event => {
                const blocked = staleFieldNames
                    .map(name => [name, findField(name)])
                    .find(([name, field]) => field && !acknowledged.has(name) && fieldValue(field) === initialValues.get(name));

                if (!blocked) return;

                event.preventDefault();
                alertBox?.classList.remove('hidden');
                revealField(blocked[1]);
                blocked[1].focus();
            });
        })();
    </script>
    @endpush
@endif
