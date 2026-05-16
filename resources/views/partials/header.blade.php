<header class="border-b border-gray-100">
    @php
        $isBaseLocale = app()->getLocale() === \App\Models\Locale::baseCode();
        $homeHref = $isBaseLocale
            ? route('home')
            : route('localized.home', ['locale' => app()->getLocale()]);
    @endphp
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo / Site name --}}
            <a href="{{ $homeHref }}" class="flex items-center gap-2">
                @if($siteLogoMedia ?? null)
                    <x-responsive-img
                        :media="$siteLogoMedia"
                        sizes="128px"
                        :fallback-alt="$siteName"
                        class="h-8 w-auto"
                        loading="eager"
                    />
                @elseif($siteLogo ?? null)
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-8 w-auto" loading="eager">
                @endif
                <span class="text-lg font-semibold text-gray-900">{{ $siteName }}</span>
            </a>

            {{-- Desktop nav --}}
            <x-menu-component slug="header"
                class="hidden sm:flex items-center gap-6 [&_a]:text-sm [&_a]:text-gray-600 [&_a:hover]:text-gray-900 [&_a]:transition-colors [&_.sub-menu]:hidden" />

            @php
                $languageAlternates = collect();
                $localizedEntity = $page ?? $post ?? null;
                if ($localizedEntity && method_exists($localizedEntity, 'availablePublishedTranslations')) {
                    $languageAlternates = $localizedEntity->availablePublishedTranslations()->where('locale', '!=', app()->getLocale());
                }
            @endphp
            @if($languageAlternates->isNotEmpty())
                <div class="hidden sm:flex items-center gap-2">
                    @foreach($languageAlternates as $alternate)
                        <a href="{{ $alternate->url() }}" class="text-xs font-medium text-gray-500 hover:text-gray-900">
                            {{ strtoupper($alternate->locale) }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Mobile hamburger --}}
            <button id="mobile-menu-toggle" class="sm:hidden text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        {{-- Mobile nav --}}
        <x-menu-component slug="header" id="mobile-menu"
            class="sm:hidden hidden pb-4 [&_a]:block [&_a]:py-2 [&_a]:text-sm [&_a]:text-gray-600 [&_a:hover]:text-gray-900 [&_a]:transition-colors [&_.sub-menu]:pl-4" />
    </div>
</header>

<script>
    document.getElementById('mobile-menu-toggle')?.addEventListener('click', () => {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
