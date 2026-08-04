<header class="ama-header-shell">
    @php
        $isBaseLocale = app()->getLocale() === \App\Models\Locale::baseCode();
        $homeHref = $isBaseLocale
            ? route('home')
            : route('localized.home', ['locale' => app()->getLocale()]);
    @endphp

    <div id="site-header-pill" class="ama-header-pill overflow-hidden rounded-full">
        <div class="flex min-h-[58px] items-center justify-between gap-5 px-5 sm:px-7">
            <a href="{{ $homeHref }}" class="flex min-w-0 items-center gap-3" aria-label="{{ $siteName }} home">
                @if($siteLogoMedia ?? null)
                    <x-responsive-img
                        :media="$siteLogoMedia"
                        sizes="132px"
                        :fallback-alt="$siteName"
                        class="h-9 w-auto max-w-[150px] object-contain"
                        loading="eager"
                    />
                @elseif($siteLogo ?? null)
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-9 w-auto max-w-[150px] object-contain" loading="eager">
                @else
                    <span class="font-display text-2xl leading-none tracking-[-0.01em]">{{ $siteName }}</span>
                @endif
            </a>

            <nav class="hidden lg:flex flex-1 items-center justify-center" aria-label="Primary navigation">
                <x-menu-component slug="header"
                    class="ama-nav-list flex items-center justify-center gap-8 [&>li]:relative" />
            </nav>

            @php
                $languageAlternates = collect();
                $localizedEntity = $page ?? $post ?? null;
                if ($localizedEntity && method_exists($localizedEntity, 'availablePublishedTranslations')) {
                    $languageAlternates = $localizedEntity->availablePublishedTranslations()->where('locale', '!=', app()->getLocale());
                }
            @endphp

            <div class="hidden lg:flex items-center gap-3">
                @if($languageAlternates->isNotEmpty())
                    <div class="flex items-center gap-2 border-r border-ama-ink/10 pr-3 text-[10px] uppercase tracking-[0.18em] opacity-70">
                        @foreach($languageAlternates as $alternate)
                            <a href="{{ $alternate->url() }}" class="hover:text-ama-gold">
                                {{ strtoupper($alternate->locale) }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <a href="/contact" class="ama-button-primary min-h-10 rounded-full px-5 text-[9px]">
                    Book Now
                    <span aria-hidden="true">↗</span>
                </a>
            </div>

            <button id="mobile-menu-toggle" class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-full border border-current/15" aria-expanded="false" aria-controls="mobile-menu" aria-label="Open menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </button>
        </div>

        <div id="mobile-menu" class="ama-mobile-panel hidden border-t border-current/10 px-6 pb-6 pt-2 lg:hidden">
            <x-menu-component slug="header"
                class="space-y-3 [&_a]:block [&_a]:py-1.5 [&_a]:text-sm [&_a]:text-current/75 [&_a:hover]:text-ama-gold" />
            <a href="/contact" class="ama-button-primary mt-5 w-full rounded-full">
                Book Now
                <span aria-hidden="true">↗</span>
            </a>
        </div>
    </div>
</header>
