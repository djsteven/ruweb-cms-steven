@extends('admin.layouts.app')

@section('title', __('admin.editorial_control'))

@section('content')
<div class="mb-6">
    <h1 class="text-lg font-semibold text-white">{{ __('admin.editorial_control') }}</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ __('admin.editorial_control_subtitle') }}</p>
</div>

<div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/[0.06]">
                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3">{{ __('admin.col_title') }}</th>
                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3">{{ __('admin.type') }}</th>
                @foreach($locales as $locale)
                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3">{{ strtoupper($locale->code) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-white/[0.04]">
            @forelse($items as $item)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="px-4 py-3 text-sm text-white">{{ $item['label'] }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $item['type'] }}</td>
                    @foreach($locales as $locale)
                        @php
                            $cell = $item['cells'][$locale->code] ?? ['state' => 'missing', 'action' => null];
                            $state = $cell['state'];
                            $action = $cell['action'];
                        @endphp
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $state === 'published' ? 'bg-sky-500/10 text-sky-400' : ($state === 'missing' ? 'bg-white/[0.04] text-gray-600' : 'bg-yellow-500/10 text-yellow-400') }}">
                                    {{ $state }}
                                </span>
                                @if($action && $action['kind'] === 'create')
                                    <form method="POST" action="{{ $action['url'] }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-sky-400 hover:text-sky-300 transition-colors">
                                            {{ __('admin.action_create') }}
                                        </button>
                                    </form>
                                @elseif($action && $action['kind'] === 'update')
                                    <a href="{{ $action['url'] }}" class="text-xs font-medium text-yellow-400 hover:text-yellow-300 transition-colors">
                                        {{ __('admin.action_update') }}
                                    </a>
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 2 + $locales->count() }}" class="px-4 py-10 text-center text-sm text-gray-600">
                        {{ __('admin.no_content_yet') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
