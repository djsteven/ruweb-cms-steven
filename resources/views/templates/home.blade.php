@extends('layouts.public')

@section('content')
@php
    $sections = $page->sections();
    $hero = $sections['hero'] ?? [];
    $intro = $sections['intro'] ?? [];
    $features = $sections['features'] ?? [];
    $cta = $sections['cta'] ?? [];
    $heroBackground = isset($hero['image_id']) ? \App\Models\Media::find((int) $hero['image_id']) : null;
    $heroVideoUrl = trim((string) ($hero['video_url'] ?? ''));

    if ($heroVideoUrl !== '') {
        $heroVideoUrl = str_replace('player.mediadelivery.net/play/', 'player.mediadelivery.net/embed/', $heroVideoUrl);
        $heroVideoSrc = $heroVideoUrl.(str_contains($heroVideoUrl, '?') ? '&' : '?').'autoplay=true&muted=true&loop=true&preload=true&responsive=false';
    } else {
        $heroVideoSrc = null;
    }

    $heroHeading = $hero['heading'] ?? 'Group Retreats in Costa Rica';
    $heroBody = $hero['body'] ?? 'A private forest sanctuary for retreat leaders and guests seeking renewal, connection, and quiet luxury in the hills of Costa Rica.';
    $heroButtonLabel = $hero['button_label'] ?? 'Request Availability';
    $heroButtonUrl = $hero['button_url'] ?? '/contact';
    $introEyebrow = $intro['eyebrow'] ?? 'Mountain Sanctuary · Costa Rica';
    $introHeading = $intro['heading'] ?? 'AmaTierra';
    $introBody = $intro['body'] ?? 'A private retreat sanctuary in Costa Rica’s mist-covered mountain forest — where the jungle itself becomes the teacher.';
@endphp

<section class="relative min-h-screen overflow-hidden bg-ama-ink pt-28 text-ama-bone sm:pt-32">
    <div class="ama-hero-media absolute inset-0">
        @if($heroVideoSrc)
            <iframe
                src="{{ $heroVideoSrc }}"
                title="AmaTierra retreat video"
                allow="autoplay; fullscreen; picture-in-picture"
                loading="eager"
                class="pointer-events-none absolute left-1/2 top-1/2 h-[56.25vw] min-h-full w-full min-w-[177.78vh] -translate-x-1/2 -translate-y-1/2 border-0"
                aria-hidden="true"
                tabindex="-1"
            ></iframe>
        @elseif($heroBackground)
            <x-responsive-img
                :media="$heroBackground"
                sizes="100vw"
                alt=""
                class="h-full w-full object-cover"
                loading="eager"
            />
        @else
            <div class="h-full w-full bg-[radial-gradient(circle_at_72%_30%,rgba(143,181,142,0.35),transparent_34%),linear-gradient(135deg,#24402b_0%,#0f1710_58%,#1a2a1c_100%)]"></div>
        @endif
        <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(15,23,16,0.9)_0%,rgba(15,23,16,0.66)_38%,rgba(15,23,16,0.18)_68%,rgba(15,23,16,0.42)_100%)]"></div>
        <div class="absolute inset-x-0 bottom-0 h-44 bg-gradient-to-t from-ama-ink to-transparent"></div>
    </div>

    <div class="relative z-10 flex min-h-[calc(100vh-7rem)] items-center px-6 pb-16 pt-20 sm:px-10 lg:px-section-inline">
        <div class="max-w-3xl">
            <p class="overline mb-8">AmaTierra Retreat Center</p>
            <h1 class="display-title max-w-3xl text-[64px] leading-[0.94] sm:text-[86px] lg:text-[112px]">
                {{ $heroHeading }}
            </h1>
            @if($heroBody)
                <p class="mt-7 max-w-xl text-base leading-8 text-ama-bone/70 sm:text-lg">
                    {{ $heroBody }}
                </p>
            @endif
            <div class="mt-9 flex flex-wrap items-center gap-4">
                <a href="{{ $heroButtonUrl }}" class="ama-button-primary">
                    {{ $heroButtonLabel }}
                    <span aria-hidden="true">↗</span>
                </a>
                <a href="/retreats" class="ama-button-secondary">
                    Explore Retreats
                </a>
            </div>
        </div>
    </div>

    <div class="absolute right-7 top-1/2 z-20 hidden -translate-y-1/2 flex-col items-center gap-4 lg:flex" aria-hidden="true">
        <span class="h-1.5 w-1.5 rounded-full bg-ama-gold"></span>
        <span class="h-10 w-10 rounded-full border border-ama-bone/35"></span>
        <span class="h-1.5 w-1.5 rounded-full bg-ama-bone/35"></span>
    </div>

    <a href="#home-intro" class="absolute bottom-0 left-1/2 z-20 hidden h-20 w-20 -translate-x-1/2 translate-y-1/2 items-start justify-center rounded-full border border-ama-gold/35 bg-ama-ink-alt pt-5 text-ama-gold sm:flex" aria-label="Scroll to content">
        <span aria-hidden="true">↓</span>
    </a>
</section>

@if(($intro['is_visible'] ?? 1) && ($introEyebrow || $introHeading || $introBody))
<section id="home-intro" class="bg-ama-ink-alt px-6 py-14 text-ama-bone sm:px-10 lg:px-section-inline lg:py-16">
    <div class="mx-auto grid max-w-[1450px] gap-10 lg:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.65fr)] lg:items-center">
        <div>
            @if($introEyebrow)
                <p class="overline mb-6">{{ $introEyebrow }}</p>
            @endif
            @if($introHeading)
                <h2 class="display-title text-[64px] italic leading-none text-ama-gold-pale sm:text-[82px] lg:text-[96px]">
                    {{ $introHeading }}
                </h2>
            @endif
        </div>
        @if($introBody)
            <p class="max-w-md text-lg leading-8 text-ama-bone/68 lg:justify-self-end">
                {{ $introBody }}
            </p>
        @endif
    </div>
</section>
@endif

@if(($features['is_visible'] ?? 1) && (($features['items'] ?? null) || ($features['heading'] ?? null)))
<section class="bg-ama-ink-alt px-6 py-20 text-ama-bone sm:px-10 lg:px-section-inline lg:py-section">
    <div class="mx-auto max-w-6xl">
        @if($features['heading'] ?? null)
            <p class="overline mb-6">Why AmaTierra</p>
            <h2 class="display-title max-w-3xl text-5xl leading-none sm:text-6xl">{{ $features['heading'] }}</h2>
        @endif

        @if($features['items'] ?? null)
            <div class="mt-12 grid gap-[2px] md:grid-cols-3">
                @foreach($features['items'] as $item)
                    <article class="border border-white/[0.06] bg-ama-ink p-8">
                        @if($item['title'] ?? null)
                            <h3 class="display-title text-3xl text-ama-bone">{{ $item['title'] }}</h3>
                        @endif
                        @if($item['body'] ?? null)
                            <p class="mt-4 leading-7 text-ama-bone/55">{{ $item['body'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

@if(($cta['is_visible'] ?? 1) && ($cta['heading'] ?? $cta['body'] ?? null))
<section class="bg-ama-ink px-6 py-20 text-ama-bone sm:px-10 lg:px-section-inline lg:py-section">
    <div class="mx-auto max-w-4xl text-center">
        <p class="overline justify-center mb-6">Begin Planning</p>
        @if($cta['heading'] ?? null)
            <h2 class="display-title text-5xl leading-none sm:text-6xl">{{ $cta['heading'] }}</h2>
        @endif
        @if($cta['body'] ?? null)
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-ama-bone/60">{{ $cta['body'] }}</p>
        @endif
        <a href="{{ $cta['button_url'] ?? '/contact' }}" class="ama-button-primary mt-9">
            {{ $cta['button_label'] ?? 'Request availability' }}
        </a>
    </div>
</section>
@endif

@if($sections['google_reviews']['is_visible'] ?? 1)
    <x-google-reviews />
@endif
@endsection
