@php($retreat = $retreat ?? null)
<form method="POST" action="{{ $retreat ? route('admin.retreats.update', $retreat) : route('admin.retreats.store') }}" class="space-y-6">
    @csrf
    @if($retreat) @method('PUT') @endif
    <div class="grid gap-5 md:grid-cols-2">
        <div class="md:col-span-2"><label class="mb-1 block text-xs text-gray-500">Event title</label><input required name="title" value="{{ old('title', $retreat?->title) }}" class="w-full rounded-md border border-white/10 bg-[#1a1a1a] px-3 py-2 text-white"></div>
        <div class="md:col-span-2"><label class="mb-1 block text-xs text-gray-500">Slug</label><input required name="slug" value="{{ old('slug', $retreat?->slug) }}" placeholder="event-name" class="w-full rounded-md border border-white/10 bg-[#1a1a1a] px-3 py-2 text-white"></div>
        <div><label class="mb-1 block text-xs text-gray-500">From</label><input required type="date" name="starts_at" value="{{ old('starts_at', $retreat?->starts_at?->format('Y-m-d')) }}" class="w-full rounded-md border border-white/10 bg-[#1a1a1a] px-3 py-2 text-white"></div>
        <div><label class="mb-1 block text-xs text-gray-500">To</label><input required type="date" name="ends_at" value="{{ old('ends_at', $retreat?->ends_at?->format('Y-m-d')) }}" class="w-full rounded-md border border-white/10 bg-[#1a1a1a] px-3 py-2 text-white"></div>
        <div class="md:col-span-2"><label class="mb-1 block text-xs text-gray-500">Organizer</label><input name="organizer" value="{{ old('organizer', $retreat?->organizer) }}" class="w-full rounded-md border border-white/10 bg-[#1a1a1a] px-3 py-2 text-white"></div>
        <div class="md:col-span-2">@include('admin.media._selector', ['name' => 'featured_image', 'value' => old('featured_image', $retreat?->featuredImage()?->id), 'label' => 'Main image'])</div>
        <div class="md:col-span-2"><label class="mb-1 block text-xs text-gray-500">Card summary</label><textarea name="excerpt" rows="3" class="w-full rounded-md border border-white/10 bg-[#1a1a1a] px-3 py-2 text-white">{{ old('excerpt', $retreat?->excerpt) }}</textarea></div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-xs text-gray-500">Event description</label>
            <div class="flex flex-wrap gap-1 rounded-t-md border border-white/10 bg-[#222] p-2" data-rich-toolbar>
                <button type="button" data-command="formatBlock" data-value="h2" class="rounded px-2 py-1 text-xs text-gray-300 hover:bg-white/10">Title</button>
                <button type="button" data-command="bold" class="rounded px-2 py-1 text-xs font-bold text-gray-300 hover:bg-white/10">Bold</button>
                <button type="button" data-command="italic" class="rounded px-2 py-1 text-xs italic text-gray-300 hover:bg-white/10">Italic</button>
                <button type="button" data-command="insertUnorderedList" class="rounded px-2 py-1 text-xs text-gray-300 hover:bg-white/10">List</button>
                <button type="button" data-command="createLink" class="rounded px-2 py-1 text-xs text-gray-300 hover:bg-white/10">Link</button>
                <button type="button" data-command="insertImage" class="rounded px-2 py-1 text-xs text-gray-300 hover:bg-white/10">Image</button>
            </div>
            <div id="retreat-rich-editor" contenteditable="true" class="prose prose-invert min-h-72 max-w-none rounded-b-md border border-t-0 border-white/10 bg-[#1a1a1a] p-4 text-gray-200 focus:outline-none">{!! old('content', $retreat?->content) !!}</div>
            <textarea hidden name="content" id="retreat-content"></textarea>
        </div>
        <div><label class="mb-1 block text-xs text-gray-500">Status</label><select name="status" class="w-full rounded-md border border-white/10 bg-[#1a1a1a] px-3 py-2 text-white">@foreach(config('cms.statuses') as $status)<option value="{{ $status }}" @selected(old('status', $retreat?->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div><label class="mb-1 block text-xs text-gray-500">Publish at</label><input type="datetime-local" name="published_at" value="{{ old('published_at', $retreat?->published_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-md border border-white/10 bg-[#1a1a1a] px-3 py-2 text-white"></div>
    </div>
    @if($errors->any())<div class="rounded bg-red-950/40 p-3 text-sm text-red-300">{{ $errors->first() }}</div>@endif
    <button class="rounded-md bg-sky-600 px-5 py-2 text-sm font-medium text-white hover:bg-sky-500">Save retreat</button>
</form>

@push('scripts')
<script>
    const retreatForm = document.querySelector('form[action*="retreats"]');
    const retreatEditor = document.getElementById('retreat-rich-editor');
    document.querySelectorAll('[data-rich-toolbar] button').forEach(button => button.addEventListener('click', () => {
        let value = button.dataset.value || null;
        if (button.dataset.command === 'createLink' || button.dataset.command === 'insertImage') value = prompt('Enter the URL:');
        if (value !== null || !['createLink', 'insertImage'].includes(button.dataset.command)) document.execCommand(button.dataset.command, false, value);
        retreatEditor.focus();
    }));
    retreatForm?.addEventListener('submit', () => document.getElementById('retreat-content').value = retreatEditor.innerHTML);
</script>
@endpush
