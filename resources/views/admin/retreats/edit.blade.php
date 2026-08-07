@extends('admin.layouts.app')
@section('title', 'Edit Retreat')
@section('content')<div class="mx-auto" style="max-width: 1480px"><div class="mb-6 flex items-center justify-between"><div><h1 class="text-xl font-semibold text-white">Edit Upcoming Retreat</h1><p class="mt-1 text-sm text-gray-500">Build the event page and control how it appears in retreat cards.</p></div><a target="_blank" href="{{ route('retreats.show', $retreat) }}" class="text-sm text-sky-400 hover:text-sky-300">View event</a></div>@include('admin.retreats._form')</div>@endsection
