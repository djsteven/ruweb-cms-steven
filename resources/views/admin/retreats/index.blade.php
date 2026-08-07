@extends('admin.layouts.app')
@section('title', 'Upcoming Retreats')
@section('content')
<div class="mb-6 flex items-center justify-between"><h1 class="text-lg font-semibold text-white">Upcoming Retreats</h1><a href="{{ route('admin.retreats.create') }}" class="rounded-md bg-sky-600 px-3 py-2 text-sm text-white">New retreat</a></div>
<div class="overflow-hidden rounded-lg border border-white/[0.06] bg-[#151515]">
@forelse($retreats as $retreat)
<div class="flex items-center justify-between border-b border-white/[0.06] p-4"><div><a class="font-medium text-white hover:text-sky-400" href="{{ route('admin.retreats.edit', $retreat) }}">{{ $retreat->title }}</a><p class="mt-1 text-xs text-gray-500">{{ $retreat->starts_at->format('M j, Y') }} – {{ $retreat->ends_at->format('M j, Y') }} · {{ ucfirst($retreat->status) }}</p></div><a class="text-sm text-gray-400" href="{{ route('admin.retreats.edit', $retreat) }}">Edit</a></div>
@empty <p class="p-8 text-center text-gray-500">No retreats yet.</p> @endforelse
</div><div class="mt-4">{{ $retreats->links() }}</div>
@endsection
