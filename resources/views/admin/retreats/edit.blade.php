@extends('admin.layouts.app')
@section('title', 'Edit Retreat')
@section('content')<div class="mx-auto max-w-4xl"><div class="mb-6 flex justify-between"><h1 class="text-lg font-semibold text-white">Edit Upcoming Retreat</h1><a target="_blank" href="{{ route('retreats.show', $retreat) }}" class="text-sm text-sky-400">View event</a></div>@include('admin.retreats._form')</div>@endsection
