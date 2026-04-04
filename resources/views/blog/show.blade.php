@extends('layouts.public')

@section('content')
<article class="py-14 sm:py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-800 mb-6 transition-colors">
            ← Back to blog
        </a>

        <header>
            <p class="text-sm text-gray-500 mb-2">{{ optional($post->published_at)->format('M d, Y') }}</p>
            <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-gray-900">{{ $post->title }}</h1>
            @if($post->excerpt)
                <p class="mt-4 text-lg text-gray-600">{{ $post->excerpt }}</p>
            @endif
        </header>

        @if($post->featuredImage())
            <figure class="mt-8">
                <img src="{{ $post->featuredImage()->url() }}" alt="{{ $post->featuredImage()->alt ?: $post->title }}" class="w-full rounded-xl object-cover">
            </figure>
        @endif

        @if($post->content)
            <div class="prose prose-gray max-w-none mt-8">
                {!! nl2br(e($post->content)) !!}
            </div>
        @endif
    </div>
</article>
@endsection
