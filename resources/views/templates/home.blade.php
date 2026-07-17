@extends('layouts.public')

@section('content')
@php
    $sections = $page->sections();
    $hero = $sections['hero'] ?? [];
    $features = $sections['features'] ?? [];
    $cta = $sections['cta'] ?? [];
    $heroBackground = isset($hero['image_id']) ? \App\Models\Media::find((int) $hero['image_id']) : null;
@endphp

{{-- Hero --}}
@if(($hero['is_visible'] ?? 1) && ($hero['heading'] ?? null))
<section class="relative overflow-hidden py-20 sm:py-28 {{ $heroBackground ? 'bg-gray-900' : 'bg-gray-50' }}">
    @if($heroBackground)
        <div class="absolute inset-0">
            <x-responsive-img
                :media="$heroBackground"
                sizes="100vw"
                alt=""
                class="h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-black/45"></div>
        </div>
    @endif
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold tracking-tight {{ $heroBackground ? 'text-white' : 'text-gray-900' }}">{{ $hero['heading'] }}</h1>
        @if($hero['body'] ?? null)
            <p class="mt-6 text-lg sm:text-xl max-w-2xl mx-auto {{ $heroBackground ? 'text-gray-100' : 'text-gray-600' }}">{{ $hero['body'] }}</p>
        @endif
    </div>
</section>
@endif

{{-- Features --}}
@if(($features['is_visible'] ?? 1) && ($features['heading'] ?? $features['body'] ?? null))
<section class="py-16 sm:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($features['heading'] ?? null)
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-4">{{ $features['heading'] }}</h2>
        @endif
        @if($features['body'] ?? null)
            <div class="prose prose-gray max-w-none text-center">
                {!! nl2br(e($features['body'])) !!}
            </div>
        @endif
    </div>
</section>
@endif

{{-- CTA --}}
@if(($cta['is_visible'] ?? 1) && ($cta['heading'] ?? $cta['body'] ?? null))
<section class="bg-gray-900 py-16 sm:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        @if($cta['heading'] ?? null)
            <h2 class="text-3xl font-bold text-white mb-4">{{ $cta['heading'] }}</h2>
        @endif
        @if($cta['body'] ?? null)
            <p class="text-lg text-gray-300 max-w-2xl mx-auto">{{ $cta['body'] }}</p>
        @endif
    </div>
</section>
@endif

@if($sections['google_reviews']['is_visible'] ?? 1)
    <x-google-reviews />
@endif
@endsection
