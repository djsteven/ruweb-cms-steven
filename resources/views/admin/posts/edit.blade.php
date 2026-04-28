@extends('admin.layouts.editor')

@section('editor-title', $post->title)

@section('editor-actions')
    @if ($post->isPublished())
        <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
           class="text-xs text-gray-500 hover:text-gray-300 transition-colors hidden sm:inline">
            {{ __('admin.view_live') }}
        </a>
    @endif
@endsection

@section('editor-form')
    @include('admin.posts._form', ['post' => $post])
@endsection

@section('editor-footer')
    @include('admin.partials._editor-footer', ['model' => $post])
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    initEditorEngine({
        previewUrl: '{{ route('admin.posts.preview', $post) }}',
        savedMsg: '{{ __('admin.saved_success') }}',
        errorMsg: '{{ __('admin.save_error') }}',
    });
});
</script>
@endpush
