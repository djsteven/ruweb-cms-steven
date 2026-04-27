@extends('admin.layouts.app')

@section('title', __('admin.media_health'))

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-lg font-semibold text-white">{{ __('admin.media_health') }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ __('admin.media_health_subtitle') }}</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-300 transition-colors">
        {{ __('admin.dashboard') }}
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('admin.total_raster_eligible') }}</div>
        <div class="text-2xl font-semibold text-white mt-2">{{ $detail['total_raster_eligible'] }}</div>
    </div>
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('admin.total_optimized') }}</div>
        <div class="text-2xl font-semibold text-white mt-2">{{ $detail['total_optimized'] }}</div>
    </div>
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('admin.total_with_variants') }}</div>
        <div class="text-2xl font-semibold text-white mt-2">{{ $detail['total_with_variants'] }}</div>
    </div>
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('admin.total_saved_mb') }}</div>
        <div class="text-2xl font-semibold text-emerald-400 mt-2">{{ $detail['total_saved_mb'] }}</div>
    </div>
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('admin.average_reduction_ratio') }}</div>
        <div class="text-2xl font-semibold text-white mt-2">{{ $detail['average_reduction_ratio'] }}%</div>
    </div>
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('admin.preserved_originals') }}</div>
        <div class="text-2xl font-semibold text-white mt-2">{{ $detail['preserved_originals'] }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5">
        <h2 class="text-sm font-semibold text-white mb-3">{{ __('admin.health_issues') }}</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between text-gray-400">
                <span>{{ __('admin.missing_files') }}</span>
                <span class="text-white">{{ $detail['missing_physical_files'] }}</span>
            </div>
            <div class="flex justify-between text-gray-400">
                <span>{{ __('admin.missing_dimensions') }}</span>
                <span class="text-white">{{ $detail['missing_dimensions'] }}</span>
            </div>
            <div class="flex justify-between text-gray-400">
                <span>{{ __('admin.missing_variants') }}</span>
                <span class="text-white">{{ $detail['missing_variants'] }}</span>
            </div>
        </div>
    </div>
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5">
        <h2 class="text-sm font-semibold text-white mb-3">{{ __('admin.coverage') }}</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between text-gray-400">
                <span>{{ __('admin.webp_coverage') }}</span>
                <span class="text-white">{{ $summary['webp_coverage_percent'] }}%</span>
            </div>
            <div class="flex justify-between text-gray-400">
                <span>{{ __('admin.responsive_coverage') }}</span>
                <span class="text-white">{{ $summary['responsive_coverage_percent'] }}%</span>
            </div>
            <div class="flex justify-between text-gray-400">
                <span>{{ __('admin.bytes_saved') }}</span>
                <span class="text-white">{{ number_format($summary['bytes_saved'] / 1048576, 2) }} MB</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5 overflow-x-auto">
        <h2 class="text-sm font-semibold text-white mb-3">{{ __('admin.top_savings') }}</h2>
        @if(empty($detail['top_savings']))
            <p class="text-sm text-gray-500">{{ __('admin.no_data') }}</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="pb-2">{{ __('admin.col_title') }}</th>
                        <th class="pb-2">{{ __('admin.bytes_saved') }}</th>
                        <th class="pb-2">{{ __('admin.average_reduction_ratio') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach($detail['top_savings'] as $row)
                        <tr>
                            <td class="py-1">{{ $row['filename'] }}</td>
                            <td class="py-1">{{ number_format($row['saved_bytes'] / 1048576, 2) }} MB</td>
                            <td class="py-1">{{ $row['saved_percent'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5 overflow-x-auto">
        <h2 class="text-sm font-semibold text-white mb-3">{{ __('admin.top_pending_optimization') }}</h2>
        @if(empty($detail['top_pending_optimization']))
            <p class="text-sm text-gray-500">{{ __('admin.no_data') }}</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="pb-2">{{ __('admin.col_title') }}</th>
                        <th class="pb-2">{{ __('admin.col_status') }}</th>
                        <th class="pb-2">{{ __('admin.bytes_saved') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach($detail['top_pending_optimization'] as $row)
                        <tr>
                            <td class="py-1">{{ $row['filename'] }}</td>
                            <td class="py-1 uppercase">{{ $row['extension'] }}</td>
                            <td class="py-1">{{ number_format($row['size'] / 1048576, 2) }} MB</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection

