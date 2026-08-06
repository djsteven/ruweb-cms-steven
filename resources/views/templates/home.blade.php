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
