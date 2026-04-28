@extends('admin.layouts.guest')

@section('title', '500 Server Error')

@section('content')
<div class="text-center">
    <p class="text-xs font-medium text-sky-500 uppercase tracking-widest mb-4">500</p>
    <h1 class="text-2xl font-semibold text-white">Server error</h1>
    <p class="text-sm text-gray-500 mt-2">Something went wrong. Please try again later.</p>
    <a href="{{ route('admin.dashboard') }}" class="inline-block mt-6 px-4 py-2 bg-[#1a1a1a] ring-1 ring-white/10 text-gray-400 hover:text-white rounded-md text-sm transition-colors">
        Back to Dashboard
    </a>
</div>
@endsection
