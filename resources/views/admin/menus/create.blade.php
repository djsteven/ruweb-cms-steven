@extends('admin.layouts.app')

@section('title', __('admin.menu_new'))

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.menus.index') }}" class="hover:text-gray-300 transition-colors">{{ __('admin.menus') }}</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-400">{{ __('admin.menu_new') }}</span>
    </div>
    <h1 class="text-lg font-semibold text-white">{{ __('admin.menu_new') }}</h1>
</div>

<form method="POST" action="{{ route('admin.menus.store') }}" class="max-w-lg">
    @csrf

    @include('admin.menus._form', ['menu' => null])

    <div class="mt-5 flex items-center gap-3">
        <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition-colors">
            {{ __('admin.btn_save_changes') }}
        </button>
        <a href="{{ route('admin.menus.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-medium rounded-md transition-colors">
            {{ __('admin.btn_cancel') }}
        </a>
    </div>
</form>

@push('scripts')
<script>
    document.getElementById('name').addEventListener('input', function () {
        const slug = document.getElementById('slug');
        if (!slug.dataset.edited) {
            slug.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        }
    });
    document.getElementById('slug').addEventListener('input', function () {
        this.dataset.edited = '1';
    });
</script>
@endpush
@endsection
