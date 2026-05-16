@extends('admin.layouts.app')

@section('title', __('admin.menus'))

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
    <h1 class="text-lg font-semibold text-white">{{ __('admin.menus') }}</h1>

    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.menus.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('admin.menu_new') }}
    </a>
    @endif
</div>

@if ($menuGroups->isEmpty())
    <div class="text-center py-20 text-gray-600">
        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <p class="text-sm font-medium text-gray-500">{{ __('admin.menu_no_menus_yet') }}</p>
        <p class="text-sm text-gray-600 mt-1">{{ __('admin.menu_no_menus_hint') }}</p>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.menus.create') }}" class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition-colors">
            {{ __('admin.menu_new') }}
        </a>
        @endif
    </div>
@else
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/[0.06]">
                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3">{{ __('admin.menu_col_name') }}</th>
                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3 hidden sm:table-cell">{{ __('admin.menu_col_slug') }}</th>
                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3 hidden sm:table-cell">{{ __('admin.language') }}</th>
                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3 hidden md:table-cell">{{ __('admin.menu_col_location') }}</th>
                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3 hidden md:table-cell">{{ __('admin.menu_col_items') }}</th>
                    <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3">{{ __('admin.menu_col_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[0.04]">
                @foreach ($menuGroups as $group)
                    @php
                        $menu = $group['primary'];
                        $translations = $group['translations'];
                    @endphp
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.menus.edit', $menu) }}" class="text-sm text-white hover:text-sky-400 transition-colors">
                                {{ $menu->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <span class="text-sm text-gray-500">{{ $menu->slug }}</span>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <div class="flex items-center gap-1">
                                @foreach(($locales ?? collect()) as $locale)
                                    @php
                                        $translation = $translations->get($locale->code);
                                        $badgeColor = $translation
                                            ? 'bg-sky-500/10 text-sky-400 hover:ring-1 hover:ring-white/20'
                                            : 'bg-white/[0.04] text-gray-600 hover:bg-white/[0.08] hover:text-gray-300';
                                    @endphp
                                    @if($translation)
                                        <a href="{{ route('admin.menus.edit', $translation) }}"
                                           title="{{ __('admin.edit_translation', ['lang' => $locale->name]) }}"
                                           class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium transition-colors {{ $badgeColor }}">
                                            {{ strtoupper($locale->code) }}
                                        </a>
                                    @elseif(auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('admin.menus.translate', [$menu, $locale->code]) }}" class="inline-flex">
                                            @csrf
                                            <button type="submit"
                                                    title="{{ __('admin.create_translation', ['lang' => $locale->name]) }}"
                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium transition-colors {{ $badgeColor }}">
                                                +{{ strtoupper($locale->code) }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium {{ $badgeColor }}">
                                            +{{ strtoupper($locale->code) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="text-sm text-gray-500">
                                {{ config('cms.menu_locations.' . $menu->location, $menu->location ?: '—') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="text-sm text-gray-500">{{ $group['items_count'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.menus.edit', $menu) }}" class="text-gray-600 hover:text-gray-400 transition-colors" title="{{ __('admin.action_edit') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}"
                                      onsubmit="return confirm('{{ __('admin.menu_confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-600 hover:text-red-400 transition-colors" title="{{ __('admin.menu_btn_delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endif
@endsection
