@php($retreat = $retreat ?? null)

<style>
    .retreat-editor-layout { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 24px; align-items: start; }
    .retreat-editor-main, .retreat-editor-sidebar { min-width: 0; }
    .retreat-editor-main { display: grid; gap: 20px; }
    .retreat-editor-sidebar { display: grid; gap: 20px; position: sticky; top: 24px; }
    .retreat-panel { overflow: hidden; border: 1px solid rgba(255,255,255,.1); border-radius: 9px; background: #12161c; box-shadow: 0 10px 30px rgba(0,0,0,.12); }
    .retreat-panel-heading { padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,.09); background: #171c23; color: #f3f4f6; font-size: 13px; font-weight: 650; }
    .retreat-panel-body { padding: 18px; }
    .retreat-fields { display: grid; gap: 18px; }
    .retreat-fields-two { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 18px; }
    .retreat-field label { display: block; margin-bottom: 7px; color: #94a3b8; font-size: 12px; }
    .retreat-control { width: 100%; border: 1px solid rgba(255,255,255,.11); border-radius: 6px; background: #1a1f26; padding: 10px 12px; color: white; outline: none; }
    .retreat-control:focus { border-color: #0ea5e9; box-shadow: 0 0 0 2px rgba(14,165,233,.15); }
    .retreat-summary { min-height: 128px; resize: vertical; line-height: 1.55; }
    .retreat-editor-shell { border: 1px solid rgba(255,255,255,.12); border-radius: 7px; overflow: hidden; background: #1a1f26; }
    .retreat-editor-tabs { display: flex; justify-content: flex-end; gap: 2px; padding: 0 12px; background: #11151b; border-bottom: 1px solid rgba(255,255,255,.09); }
    .retreat-editor-tab { border: 0; border-bottom: 2px solid transparent; padding: 10px 13px 8px; background: transparent; color: #94a3b8; font-size: 12px; cursor: pointer; }
    .retreat-editor-tab.is-active { border-bottom-color: #38bdf8; color: #f8fafc; }
    .retreat-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 4px; padding: 9px 10px; background: #171c23; border-bottom: 1px solid rgba(255,255,255,.09); }
    .retreat-toolbar button, .retreat-toolbar select { min-height: 31px; border: 1px solid rgba(255,255,255,.1); border-radius: 4px; background: #212832; color: #dbe3ee; font-size: 12px; }
    .retreat-toolbar button { min-width: 32px; padding: 5px 9px; cursor: pointer; }
    .retreat-toolbar button:hover { background: #2d3744; color: white; }
    .retreat-toolbar select { min-width: 124px; padding: 4px 8px; }
    .retreat-toolbar-separator { width: 1px; height: 25px; margin: 0 4px; background: rgba(255,255,255,.1); }
    .retreat-media-button { margin-right: 5px; border-color: rgba(56,189,248,.45) !important; color: #7dd3fc !important; }
    #retreat-rich-editor { min-height: 560px; padding: 24px; color: #e5e7eb; line-height: 1.7; outline: none; }
    #retreat-rich-editor h2 { margin: 1.2em 0 .55em; color: white; font-size: 1.65rem; font-weight: 650; line-height: 1.25; }
    #retreat-rich-editor h3 { margin: 1.15em 0 .5em; color: white; font-size: 1.3rem; font-weight: 650; }
    #retreat-rich-editor p { margin: 0 0 1em; }
    #retreat-rich-editor ul, #retreat-rich-editor ol { margin: .6em 0 1em 1.5em; }
    #retreat-rich-editor ul { list-style: disc; } #retreat-rich-editor ol { list-style: decimal; }
    #retreat-rich-editor blockquote { margin: 1em 0; padding: .4em 1em; border-left: 3px solid #38bdf8; color: #cbd5e1; }
    #retreat-rich-editor a { color: #38bdf8; text-decoration: underline; }
    #retreat-rich-editor img { max-width: 100%; height: auto; margin: 1em auto; }
    #retreat-code-editor { display: none; width: 100%; min-height: 560px; border: 0; background: #10141a; padding: 20px; color: #d1d5db; font: 13px/1.6 ui-monospace, SFMono-Regular, Menlo, monospace; outline: none; resize: vertical; }
    .retreat-publish-actions { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding-top: 4px; }
    .retreat-save-button { border: 0; border-radius: 6px; background: #0284c7; padding: 10px 17px; color: white; font-size: 13px; font-weight: 650; cursor: pointer; }
    .retreat-save-button:hover { background: #0ea5e9; }
    @media (max-width: 1050px) {
        .retreat-editor-layout { grid-template-columns: 1fr; }
        .retreat-editor-sidebar { position: static; }
    }
    @media (max-width: 640px) {
        .retreat-fields-two { grid-template-columns: 1fr; }
        .retreat-panel-body { padding: 14px; }
        #retreat-rich-editor, #retreat-code-editor { min-height: 420px; }
    }
</style>

<form method="POST" action="{{ $retreat ? route('admin.retreats.update', $retreat) : route('admin.retreats.store') }}">
    @csrf
    @if($retreat) @method('PUT') @endif

    <div class="retreat-editor-layout">
        <div class="retreat-editor-main">
            <section class="retreat-panel">
                <div class="retreat-panel-heading">Event details</div>
                <div class="retreat-panel-body retreat-fields">
                    <div class="retreat-fields-two">
                        <div class="retreat-field"><label for="retreat-starts-at">From</label><input id="retreat-starts-at" required type="date" name="starts_at" value="{{ old('starts_at', $retreat?->starts_at?->format('Y-m-d')) }}" class="retreat-control"></div>
                        <div class="retreat-field"><label for="retreat-ends-at">To</label><input id="retreat-ends-at" required type="date" name="ends_at" value="{{ old('ends_at', $retreat?->ends_at?->format('Y-m-d')) }}" class="retreat-control"></div>
                    </div>
                    <div class="retreat-field"><label for="retreat-organizer">Organizer</label><input id="retreat-organizer" name="organizer" value="{{ old('organizer', $retreat?->organizer) }}" class="retreat-control"></div>
                    <div class="retreat-field"><label for="retreat-excerpt">Card summary</label><textarea id="retreat-excerpt" name="excerpt" class="retreat-control retreat-summary">{{ old('excerpt', $retreat?->excerpt) }}</textarea></div>
                </div>
            </section>

            <section class="retreat-panel">
                <div class="retreat-panel-heading">Event description</div>
                <div class="retreat-panel-body">
                    <div class="retreat-editor-shell">
                        <div class="retreat-editor-tabs">
                            <button type="button" class="retreat-editor-tab is-active" data-editor-tab="visual">Visual</button>
                            <button type="button" class="retreat-editor-tab" data-editor-tab="code">Code</button>
                        </div>
                        <div class="retreat-toolbar" data-rich-toolbar>
                            <button type="button" class="retreat-media-button" data-command="insertImage">＋ Add media</button>
                            <select data-format aria-label="Text format">
                                <option value="p">Paragraph</option>
                                <option value="h2">Heading 2</option>
                                <option value="h3">Heading 3</option>
                                <option value="blockquote">Quote</option>
                            </select>
                            <span class="retreat-toolbar-separator"></span>
                            <button type="button" data-command="bold" title="Bold"><strong>B</strong></button>
                            <button type="button" data-command="italic" title="Italic"><em>I</em></button>
                            <button type="button" data-command="insertUnorderedList" title="Bulleted list">• List</button>
                            <button type="button" data-command="insertOrderedList" title="Numbered list">1. List</button>
                            <button type="button" data-command="formatBlock" data-value="blockquote" title="Quote">❝</button>
                            <span class="retreat-toolbar-separator"></span>
                            <button type="button" data-command="justifyLeft" title="Align left">≡</button>
                            <button type="button" data-command="justifyCenter" title="Align center">≡</button>
                            <button type="button" data-command="justifyRight" title="Align right">≡</button>
                            <span class="retreat-toolbar-separator"></span>
                            <button type="button" data-command="createLink" title="Insert link">Link</button>
                            <button type="button" data-command="unlink" title="Remove link">Unlink</button>
                        </div>
                        <div id="retreat-rich-editor" contenteditable="true">{!! old('content', $retreat?->content) !!}</div>
                        <textarea id="retreat-code-editor" aria-label="HTML code editor"></textarea>
                    </div>
                    <textarea hidden name="content" id="retreat-content"></textarea>
                </div>
            </section>
        </div>

        <aside class="retreat-editor-sidebar">
            <section class="retreat-panel">
                <div class="retreat-panel-heading">Event</div>
                <div class="retreat-panel-body retreat-fields">
                    <div class="retreat-field"><label for="retreat-title">Event title</label><input id="retreat-title" required name="title" value="{{ old('title', $retreat?->title) }}" class="retreat-control"></div>
                    <div class="retreat-field"><label for="retreat-slug">Slug</label><input id="retreat-slug" required name="slug" value="{{ old('slug', $retreat?->slug) }}" placeholder="event-name" class="retreat-control"></div>
                    <div class="retreat-field">@include('admin.media._selector', ['name' => 'featured_image', 'value' => old('featured_image', $retreat?->featuredImage()?->id), 'label' => 'Main image'])</div>
                </div>
            </section>

            <section class="retreat-panel">
                <div class="retreat-panel-heading">Publish</div>
                <div class="retreat-panel-body retreat-fields">
                    <div class="retreat-field"><label for="retreat-status">Status</label><select id="retreat-status" name="status" class="retreat-control">@foreach(config('cms.statuses') as $status)<option value="{{ $status }}" @selected(old('status', $retreat?->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                    <div class="retreat-field"><label for="retreat-published-at">Publish at</label><input id="retreat-published-at" type="datetime-local" name="published_at" value="{{ old('published_at', $retreat?->published_at?->format('Y-m-d\TH:i')) }}" class="retreat-control"></div>
                    <div class="retreat-publish-actions">
                        @if($retreat)<a target="_blank" href="{{ route('retreats.show', $retreat) }}" class="text-xs text-sky-400 hover:text-sky-300">Preview event</a>@else<span></span>@endif
                        <button class="retreat-save-button">{{ $retreat ? 'Update' : 'Publish' }}</button>
                    </div>
                </div>
            </section>
        </aside>
    </div>

    @if($errors->any())<div class="mt-5 rounded bg-red-950/40 p-3 text-sm text-red-300">{{ $errors->first() }}</div>@endif
</form>

@push('scripts')
<script>
(() => {
    const form = document.querySelector('form[action*="retreats"]');
    const visualEditor = document.getElementById('retreat-rich-editor');
    const codeEditor = document.getElementById('retreat-code-editor');
    const contentField = document.getElementById('retreat-content');
    const toolbar = document.querySelector('[data-rich-toolbar]');
    if (!form || !visualEditor || !codeEditor || !contentField || !toolbar) return;

    toolbar.querySelector('[data-format]')?.addEventListener('change', event => {
        document.execCommand('formatBlock', false, event.target.value);
        visualEditor.focus();
    });

    toolbar.querySelectorAll('button[data-command]').forEach(button => button.addEventListener('click', () => {
        const command = button.dataset.command;
        let value = button.dataset.value || null;
        if (command === 'createLink') value = prompt('Enter the link URL:');
        if (command === 'insertImage') value = prompt('Enter the image URL:');
        if ((command === 'createLink' || command === 'insertImage') && !value) return;
        document.execCommand(command, false, value);
        visualEditor.focus();
    }));

    document.querySelectorAll('[data-editor-tab]').forEach(tab => tab.addEventListener('click', () => {
        const showCode = tab.dataset.editorTab === 'code';
        if (showCode) codeEditor.value = visualEditor.innerHTML;
        else visualEditor.innerHTML = codeEditor.value;
        visualEditor.style.display = showCode ? 'none' : 'block';
        codeEditor.style.display = showCode ? 'block' : 'none';
        toolbar.style.display = showCode ? 'none' : 'flex';
        document.querySelectorAll('[data-editor-tab]').forEach(item => item.classList.toggle('is-active', item === tab));
    }));

    form.addEventListener('submit', () => {
        const codeMode = codeEditor.style.display === 'block';
        contentField.value = codeMode ? codeEditor.value : visualEditor.innerHTML;
    });
})();
</script>
@endpush
