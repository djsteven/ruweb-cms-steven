@extends('layouts.public')
@section('content')
<article class="bg-[#FAF8F3] px-6 pb-24 pt-36 text-ama-ink sm:px-10 lg:px-section-inline">
<div class="mx-auto max-w-4xl"><p class="mb-4 text-sm uppercase tracking-[.18em] text-[#7D874C]">{{ $retreat->starts_at->format('F j') }} – {{ $retreat->ends_at->format('F j, Y') }}</p><h1 class="font-display text-5xl leading-tight sm:text-7xl">{{ $retreat->title }}</h1>@if($retreat->organizer)<p class="mt-4 text-lg text-ama-ink/60">Hosted by {{ $retreat->organizer }}</p>@endif
@if($retreat->featuredImage())<x-responsive-img :media="$retreat->featuredImage()" sizes="(min-width:1024px) 896px, 100vw" :fallback-alt="$retreat->title" class="mt-10 aspect-[16/9] w-full rounded-3xl object-cover" />@endif
@if($retreat->content)<div class="prose prose-lg mt-12 max-w-none">{!! $retreat->content !!}</div>@endif</div></article>
@endsection
