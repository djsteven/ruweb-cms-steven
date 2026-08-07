@extends('layouts.public')

@section('content')
@php
    $sections = $page->sections();
    $hero = $sections['hero'] ?? [];
    $intro = $sections['intro'] ?? [];
    $audience = $sections['audience'] ?? [];
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
    $introEyebrow = $intro['eyebrow'] ?? 'Mountain Sanctuary · Costa Rica';
    $introHeading = $intro['heading'] ?? 'AmaTierra';
    $introBody = $intro['body'] ?? 'A private retreat sanctuary in Costa Rica’s mist-covered mountain forest — where the jungle itself becomes the teacher.';
    $defaultAudiencePanels = [
        [
            'eyebrow' => 'For Retreat Leaders',
            'heading' => "Host\na Retreat",
            'body' => 'Everything a retreat leader needs in one place — open-air yoga pavilions, private accommodation, nourishing on-site meals, and a dedicated team handling every logistical detail, so you focus entirely on your group.',
            'button_label' => 'Host a Retreat',
            'button_url' => '/contact',
            'buttons' => [
                ['label' => 'LETS START', 'url' => '/contact'],
            ],
            'image_id' => null,
        ],
        [
            'eyebrow' => 'For Individual Guests',
            'heading' => "Join\na Retreat",
            'body' => 'Travel solo or with friends to one of our scheduled group yoga retreats. Experience deep community, skilled teachers, and a connection to nature that reshapes how you move through the world.',
            'button_label' => 'Join a Retreat',
            'button_url' => '/retreats',
            'buttons' => [
                ['label' => 'SEE UPCOMING RETREATS', 'url' => '/retreats'],
                ['label' => 'CONTACT US', 'url' => '/contact'],
            ],
            'image_id' => null,
        ],
    ];
    $savedAudiencePanels = $audience['panels'] ?? [];
    $audiencePanels = collect($defaultAudiencePanels)
        ->map(fn ($defaultPanel, $index) => array_merge($defaultPanel, $savedAudiencePanels[$index] ?? []))
        ->all();

    $defaultOffersCopy = "Support for group leaders to host memorable retreats at reasonable rates.\nA variety of retreats for individuals to choose from where you can immerse in nature and renew your Spirit.\nA supportive environment for reflection and healing where you can be yourself and transform your energy to become more positive and whole.";
    $offersHeadingTop = $features['heading_top'] ?? 'What Ama';
    $offersHeadingAccent = $features['heading_accent'] ?? 'Tierra Offers';
    $offersCopy = trim((string) ($features['body'] ?? $defaultOffersCopy));
    $offerCopyLines = collect(preg_split('/\r\n|\r|\n/', $offersCopy))
        ->map(fn ($line) => trim($line))
        ->filter()
        ->values();
    $defaultOfferCards = [
        [
            'title' => 'Mountain rainforest location',
            'body' => 'quiet, cool, and immersive',
            'image_id' => null,
            'fallback_image' => 'Amatierra Group Retreat Costa Rica.jpg',
        ],
        [
            'title' => 'Easy access from San José',
            'body' => 'with transportation support',
            'image_id' => null,
            'fallback_image' => 'Join a Yoga Retreat at Amatierra.jpg',
        ],
    ];
    $savedOfferCards = collect($features['cards'] ?? [])->values();
    $offerCards = collect($defaultOfferCards)
        ->map(fn ($defaultCard, $index) => array_merge($defaultCard, $savedOfferCards->get($index, [])))
        ->concat($savedOfferCards->slice(count($defaultOfferCards)))
        ->filter(fn ($card) => filled($card['title'] ?? null) || filled($card['body'] ?? null) || filled($card['image_id'] ?? null))
        ->values()
        ->all();
    $upcomingRetreats = \App\Models\Retreat::query()->published()->upcoming()->with('media')->limit(3)->get();
@endphp

<section class="relative min-h-[640px] overflow-hidden bg-ama-ink pt-28 text-ama-bone sm:pt-32 lg:min-h-[66vh] lg:pt-24">
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

    <div class="relative z-10 flex min-h-[430px] items-center px-6 pb-16 pt-20 sm:px-10 lg:min-h-[calc(66vh-6rem)] lg:px-section-inline lg:pb-10 lg:pt-12">
        <div class="max-w-3xl">
            <h1 class="display-title max-w-3xl text-[46px] leading-[0.98] sm:text-[60px] lg:text-[76px] xl:text-[84px]">
                {{ $heroHeading }}
            </h1>
            @if($heroBody)
                <p class="mt-7 max-w-xl text-base leading-8 text-ama-bone/70 sm:text-lg">
                    {{ $heroBody }}
                </p>
            @endif
        </div>
    </div>
</section>

@if(($intro['is_visible'] ?? 1) && ($introEyebrow || $introHeading || $introBody))
<section id="home-intro" class="bg-ama-ink-alt px-6 py-8 text-ama-bone sm:px-10 lg:px-section-inline lg:py-10">
    <div class="mx-auto grid max-w-[1450px] gap-10 lg:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.65fr)] lg:items-center">
        <div>
            @if($introEyebrow)
                <p class="overline overline-no-rule mb-6">{{ $introEyebrow }}</p>
            @endif
            @if($introHeading)
                <h2 class="display-title text-[48px] italic leading-none text-ama-gold-pale sm:text-[60px] lg:text-[72px]">
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

@if(($audience['is_visible'] ?? 1) && count($audiencePanels))
<section class="grid bg-ama-ink text-ama-bone lg:grid-cols-2">
    @foreach($audiencePanels as $panel)
        @php
            $panelImage = isset($panel['image_id']) ? \App\Models\Media::find((int) $panel['image_id']) : null;

            if (! $panelImage) {
                $fallbackImageName = $loop->first
                    ? 'Join a Yoga Retreat at Amatierra.jpg'
                    : 'Amatierra Group Retreat Costa Rica.jpg';
                $panelImage = \App\Models\Media::where('original_filename', $fallbackImageName)->latest()->first();
            }

            $headingLines = collect(preg_split('/\r\n|\r|\n/', (string) ($panel['heading'] ?? '')))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values();
            $panelButtons = collect($panel['buttons'] ?? [])
                ->filter(fn ($button) => filled($button['label'] ?? null))
                ->values();
        @endphp
        <article class="group relative min-h-[560px] overflow-hidden lg:min-h-[720px]">
            <div class="absolute inset-0 bg-ama-moss">
                @if($panelImage)
                    <x-responsive-img
                        :media="$panelImage"
                        sizes="(min-width: 1024px) 50vw, 100vw"
                        :fallback-alt="$panel['heading'] ?? ''"
                        class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.04]"
                    />
                @else
                    <div class="h-full w-full bg-[radial-gradient(circle_at_50%_40%,rgba(143,181,142,0.2),transparent_34%),linear-gradient(135deg,#314336_0%,#1a2a1c_45%,#0f1710_100%)]"></div>
                @endif
            </div>
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(15,23,16,0.76)_0%,rgba(15,23,16,0.55)_48%,rgba(15,23,16,0.28)_100%)]"></div>
            <div class="absolute inset-0 bg-ama-ink/10 transition duration-700 group-hover:bg-ama-ink/0"></div>

            <div class="relative z-20 flex min-h-[560px] items-center px-8 py-20 sm:px-14 lg:min-h-[720px] lg:px-16 xl:px-20 2xl:px-24">
                <div class="max-w-[680px]">
                    @if($panel['eyebrow'] ?? null)
                        <p class="mb-5 inline-flex items-center gap-3 font-sans text-[13px] font-normal uppercase tracking-[0.22em] text-[#69B342] before:block before:h-px before:w-6 before:bg-[#69B342]">
                            {{ $panel['eyebrow'] }}
                        </p>
                    @endif
                    @if($headingLines->isNotEmpty())
                        <h2 class="display-title text-[58px] italic leading-[0.98] sm:text-[70px] lg:text-[78px]">
                            @foreach($headingLines as $lineIndex => $line)
                                <span class="block {{ $lineIndex === 0 ? 'text-ama-gold-pale' : 'text-ama-bone' }}">{{ $line }}</span>
                            @endforeach
                        </h2>
                    @endif
                    @if($panel['body'] ?? null)
                        <p class="mt-7 max-w-2xl text-base leading-8 text-ama-bone/72 sm:text-lg">
                            {{ $panel['body'] }}
                        </p>
                    @endif
                    @if($panelButtons->isNotEmpty())
                        <div class="relative z-30 mt-8 flex flex-wrap items-center gap-3">
                            @foreach($panelButtons as $buttonIndex => $button)
                                <a
                                    href="{{ $button['url'] ?? '#' }}"
                                    class="inline-flex min-h-11 items-center justify-center border px-5 text-[10px] font-normal uppercase tracking-[0.18em] transition {{ $buttonIndex === 0 ? 'border-ama-gold bg-ama-gold text-ama-ink hover:border-ama-gold-pale hover:bg-ama-gold-pale' : 'border-ama-bone/35 bg-transparent text-ama-bone hover:border-ama-bone/70' }}"
                                >
                                    {{ $button['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </article>
    @endforeach
</section>
@endif

@if(($features['is_visible'] ?? 1) && ($offersHeadingTop || $offersHeadingAccent || $offerCopyLines->isNotEmpty() || count($offerCards)))
<section id="home-offers" class="bg-[#FAF8F3] px-6 py-18 text-ama-ink sm:px-10 lg:px-section-inline lg:py-[104px]">
    <div class="mx-auto grid max-w-[1450px] gap-14 lg:grid-cols-[minmax(320px,0.72fr)_minmax(0,1.28fr)] lg:items-center xl:gap-16">
        <div>
            @if($offersHeadingTop || $offersHeadingAccent)
                <h2 class="display-title text-[50px] leading-[1.04] sm:text-[64px] lg:text-[68px] xl:text-[72px]">
                    @if($offersHeadingTop)
                        <span class="text-[#7D874C]">{{ $offersHeadingTop }}</span>
                    @endif
                    @if($offersHeadingAccent)
                        <span class="italic text-black"> {{ $offersHeadingAccent }}</span>
                    @endif
                </h2>
            @endif
            @if($offerCopyLines->isNotEmpty())
                <div class="mt-6 max-w-xl space-y-1 text-base leading-7 text-ama-ink/88 sm:text-[17px]">
                    @foreach($offerCopyLines as $line)
                        <p>{{ $line }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        @if(count($offerCards))
            <div class="grid gap-x-8 gap-y-12 sm:grid-cols-2 xl:grid-cols-3 xl:gap-x-10">
                @foreach($offerCards as $card)
                    @php
                        $offerImage = isset($card['image_id']) ? \App\Models\Media::find((int) $card['image_id']) : null;

                        if (! $offerImage && ($card['fallback_image'] ?? null)) {
                            $offerImage = \App\Models\Media::where('original_filename', $card['fallback_image'])->latest()->first();
                        }
                    @endphp
                    <article class="group text-center">
                        <div class="mx-auto aspect-square w-[min(250px,72vw)] overflow-hidden rounded-full bg-ama-parchment shadow-[0_22px_60px_rgba(15,23,16,0.14)] ring-1 ring-ama-ink/5 sm:w-[230px] lg:w-[240px] xl:w-[250px]">
                            @if($offerImage)
                                <x-responsive-img
                                    :media="$offerImage"
                                    sizes="(min-width: 1280px) 250px, (min-width: 1024px) 240px, 72vw"
                                    :fallback-alt="$card['title'] ?? 'AmaTierra offer'"
                                    class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.06]"
                                />
                            @else
                                <div class="h-full w-full bg-[radial-gradient(circle_at_40%_32%,rgba(143,181,142,0.45),transparent_34%),linear-gradient(135deg,#E8E0D0_0%,#F0EBE0_52%,#8FB58E_100%)]"></div>
                            @endif
                        </div>
                        @if(($card['title'] ?? null) || ($card['body'] ?? null))
                            <p class="mx-auto mt-6 max-w-[250px] text-[15px] font-semibold leading-7 text-[#7D874C] sm:text-base">
                                @if($card['title'] ?? null)
                                    <span class="block">{{ $card['title'] }}</span>
                                @endif
                                @if($card['body'] ?? null)
                                    <span class="block">{{ $card['body'] }}</span>
                                @endif
                            </p>
                        @endif
                    </article>
                @endforeach
                <div class="flex items-center justify-center gap-3 sm:col-span-2 xl:col-span-3" aria-hidden="true">
                    @foreach(range(1, 6) as $dot)
                        <span class="block size-3 rounded-full {{ $dot === 4 ? 'bg-[#25713B]' : 'bg-[#C7D2C8]' }}"></span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endif

<section class="bg-[#FAF8F3] px-6 pb-16 text-ama-bone sm:px-10 lg:px-section-inline lg:pb-24">
    <blockquote class="mx-auto max-w-[1450px] rounded-[28px] bg-[#838D4A] px-8 py-12 font-display text-3xl leading-tight sm:px-16 sm:text-4xl lg:px-20 lg:py-16 lg:text-5xl">
        “Our unique forest environment, friendly staff, comfortable accommodations and healthy, delicious food all add to a retreat experience that you will remember forever.”
    </blockquote>
</section>

@if($upcomingRetreats->isNotEmpty())
<section id="upcoming-retreats" class="relative overflow-hidden bg-[#F2ECDD] px-6 py-20 text-ama-ink sm:px-10 lg:px-section-inline lg:py-28">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.72),transparent_60%)]"></div>
    <div class="relative mx-auto max-w-[1450px]">
        <h2 class="display-title text-center text-5xl leading-[.9] text-black sm:text-6xl lg:text-7xl">Upcoming<br><span class="italic">Group Retreats</span></h2>
        <div class="mt-14 grid gap-7 lg:grid-cols-3">
            @foreach($upcomingRetreats as $retreat)
            <article class="flex flex-col rounded-[26px] bg-white p-5 shadow-[0_20px_70px_rgba(50,45,30,.09)]">
                <div class="relative aspect-[4/3] overflow-hidden rounded-[20px] bg-ama-parchment">
                    @if($retreat->featuredImage())<x-responsive-img :media="$retreat->featuredImage()" sizes="(min-width:1024px) 420px, 100vw" :fallback-alt="$retreat->title" class="h-full w-full object-cover" />@endif
                    <span class="absolute left-4 top-4 rounded-full bg-white px-5 py-2 text-sm">Available</span>
                </div>
                <p class="mt-6 text-sm">{{ $retreat->starts_at->format('j M') }} – {{ $retreat->ends_at->format('j M Y') }}</p>
                <h3 class="mt-2 font-display text-3xl leading-none">{{ $retreat->title }}</h3>
                @if($retreat->excerpt)<p class="mt-4 line-clamp-4 text-base leading-7 text-ama-ink/55">{{ $retreat->excerpt }}</p>@endif
                <div class="mt-auto flex items-center justify-between border-t border-ama-ink/10 pt-5">
                    <span class="text-sm text-[#25713B]">{{ $retreat->organizer }}</span>
                    <a href="{{ route('retreats.show', $retreat) }}" class="rounded-full bg-[#838D4A] px-6 py-2 text-sm text-white hover:bg-[#6f783e]">View Details</a>
                </div>
            </article>
            @endforeach
        </div>
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
