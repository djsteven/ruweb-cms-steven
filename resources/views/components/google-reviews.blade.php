@php
    use App\Models\GoogleReview;
    use App\Models\Setting;

    $placeId  = Setting::get('google_place_id');
    $reviews  = $placeId
        ? GoogleReview::visible()
            ->where('place_id', $placeId)
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->orderByDesc('review_time')
            ->get()
        : collect();

    $avgRating    = $placeId ? GoogleReview::averageRatingForPlace($placeId) : 0;
    $totalReviews = $placeId ? GoogleReview::totalCountForPlace($placeId) : 0;
    $fullStars    = (int) floor($avgRating);
    $hasHalf      = ($avgRating - $fullStars) >= 0.5;
    $emptyStars   = 5 - $fullStars - ($hasHalf ? 1 : 0);

    $ratingLabel = match (true) {
        $avgRating >= 4.5 => 'Excelente',
        $avgRating >= 4.0 => 'Muy bueno',
        $avgRating >= 3.0 => 'Bueno',
        $avgRating >= 2.0 => 'Regular',
        default            => 'Malo',
    };
@endphp

@if($reviews->isNotEmpty())
<section class="google-reviews-section py-16 md:py-20 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center gap-8 md:gap-6">

            {{-- Rating summary --}}
            <div class="shrink-0 md:w-56 flex flex-col items-start gap-2">
                <p class="text-2xl sm:text-3xl font-extrabold uppercase tracking-tight text-gray-900">{{ $ratingLabel }}</p>

                {{-- Stars --}}
                <div class="flex items-center gap-0.5">
                    @for ($i = 0; $i < $fullStars; $i++)
                        <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                    @if($hasHalf)
                        <svg class="w-6 h-6 text-yellow-400" viewBox="0 0 20 20">
                            <defs>
                                <linearGradient id="half-star">
                                    <stop offset="50%" stop-color="currentColor"/>
                                    <stop offset="50%" stop-color="#d1d5db"/>
                                </linearGradient>
                            </defs>
                            <path fill="url(#half-star)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endif
                    @for ($i = 0; $i < $emptyStars; $i++)
                        <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>

                <p class="text-sm text-gray-500">A base de {{ $totalReviews }} {{ $totalReviews === 1 ? 'reseña' : 'reseñas' }}</p>

                {{-- Google logo --}}
                <div class="flex items-center gap-2 mt-1">
                    <svg class="h-6 w-6 shrink-0" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    <span class="text-lg font-medium text-gray-600">Google</span>
                </div>
            </div>

            {{-- Carousel --}}
            <div class="relative flex-1 min-w-0">
                <button type="button"
                        class="hidden md:flex absolute -left-5 top-1/2 -translate-y-1/2 z-10 size-10 items-center justify-center rounded-full border border-gray-200 bg-white/90 text-gray-700 shadow-md backdrop-blur transition hover:bg-gray-900 hover:text-white hover:border-gray-900"
                        id="reviews-prev"
                        aria-label="Reseñas anteriores">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6"/>
                    </svg>
                </button>

                <div class="flex gap-5 overflow-x-auto py-1 pr-1 snap-x snap-mandatory scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                     id="reviews-track">
                    @foreach($reviews as $review)
                        @php
                            $fallbackAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($review->author_name) . '&background=f0f0f0&color=555&size=64';
                        @endphp
                        <article class="snap-start shrink-0 w-[270px] sm:w-[290px]"
                                 itemscope itemtype="https://schema.org/Review">
                            <div class="h-full bg-gray-100 rounded-2xl p-5 flex flex-col gap-3 transition hover:shadow-md">

                                {{-- Author --}}
                                <header class="flex items-center gap-3">
                                    <img src="{{ $review->avatarUrl() }}"
                                         alt="{{ $review->author_name }}"
                                         class="w-11 h-11 rounded-full object-cover bg-gray-200"
                                         loading="lazy"
                                         referrerpolicy="no-referrer"
                                         onerror="this.onerror=null;this.src='{{ $fallbackAvatar }}';">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate" itemprop="author">{{ $review->author_name }}</p>
                                        @if($review->relative_time_description)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $review->relative_time_description }}</p>
                                        @endif
                                    </div>
                                    {{-- Google icon --}}
                                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                    </svg>
                                </header>

                                {{-- Stars + verified badge --}}
                                <div class="flex items-center gap-1.5" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                                    <meta itemprop="ratingValue" content="{{ $review->rating }}">
                                    <meta itemprop="bestRating" content="5">
                                    <div class="flex items-center gap-0.5">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <svg class="w-4 h-4 {{ $s <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <svg class="w-4 h-4 text-[#1a73e8]" viewBox="0 0 24 24" fill="currentColor" aria-label="Reseña verificada">
                                        <path d="M23 12l-2.44-2.79.34-3.69-3.61-.82-1.89-3.2L12 2.96 8.6 1.5 6.71 4.69 3.1 5.5l.34 3.7L1 12l2.44 2.79-.34 3.7 3.61.82L8.6 22.5l3.4-1.47 3.4 1.46 1.89-3.19 3.61-.82-.34-3.69L23 12zm-12.91 4.72l-3.8-3.81 1.48-1.48 2.32 2.33 5.85-5.87 1.48 1.48-7.33 7.35z"/>
                                    </svg>
                                </div>

                                {{-- Review text --}}
                                @if($review->text)
                                    <p class="text-sm text-gray-700 leading-relaxed line-clamp-4" itemprop="reviewBody">
                                        {{ $review->text }}
                                    </p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                <button type="button"
                        class="hidden md:flex absolute -right-5 top-1/2 -translate-y-1/2 z-10 size-10 items-center justify-center rounded-full border border-gray-200 bg-white/90 text-gray-700 shadow-md backdrop-blur transition hover:bg-gray-900 hover:text-white hover:border-gray-900"
                        id="reviews-next"
                        aria-label="Siguientes reseñas">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile nav --}}
        <div class="flex md:hidden items-center justify-center gap-2 mt-6">
            <button type="button"
                    class="inline-flex size-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-700 shadow-sm transition hover:bg-gray-900 hover:text-white"
                    id="reviews-prev-mobile"
                    aria-label="Reseñas anteriores">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6"/>
                </svg>
            </button>
            <button type="button"
                    class="inline-flex size-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-700 shadow-sm transition hover:bg-gray-900 hover:text-white"
                    id="reviews-next-mobile"
                    aria-label="Siguientes reseñas">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/>
                </svg>
            </button>
        </div>
    </div>
</section>

<script>
(function () {
    const track = document.getElementById('reviews-track');
    if (!track) return;

    function scrollByCard(direction) {
        const card = track.querySelector('article');
        if (!card) return;
        const cardWidth = card.offsetWidth + 20; // gap-5 = 1.25rem = 20px
        track.scrollBy({ left: direction * cardWidth, behavior: 'smooth' });
    }

    ['reviews-prev', 'reviews-prev-mobile'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', () => scrollByCard(-1));
    });
    ['reviews-next', 'reviews-next-mobile'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', () => scrollByCard(1));
    });
})();
</script>
@endif
