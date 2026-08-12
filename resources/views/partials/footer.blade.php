<style>
    .ama-footer { margin-top: auto; background: #838d4a; color: #fff; }
    .ama-footer-container { width: min(calc(100% - 40px), 1200px); margin-inline: auto; }
    .ama-footer-grid { display: grid; grid-template-columns: 1.05fr .95fr 1.15fr 1.05fr; gap: 48px; align-items: start; padding-block: 64px 52px; }
    .ama-footer-logo { width: 190px; height: auto; max-height: 105px; object-fit: contain; object-position: left center; }
    .ama-footer-description { max-width: 310px; margin-top: 25px; font-size: 16px; line-height: 1.55; }
    .ama-footer-title { margin-bottom: 20px; font-family: var(--font-display); font-size: 31px; font-weight: 400; line-height: 1; }
    .ama-footer-list { display: grid; gap: 13px; font-size: 15px; line-height: 1.45; }
    .ama-footer-list a { color: inherit; transition: opacity .2s ease; }
    .ama-footer-list a:hover { opacity: .72; }
    .ama-footer-contact li { display: grid; grid-template-columns: 18px minmax(0, 1fr); gap: 10px; align-items: start; }
    .ama-footer-contact svg { width: 17px; height: 17px; margin-top: 2px; fill: currentColor; }
    .ama-footer-map { overflow: hidden; min-height: 245px; border: 8px solid rgba(255,255,255,.9); background: #f4f1e8; }
    .ama-footer-map iframe { display: block; width: 100%; height: 245px; border: 0; filter: saturate(.72) contrast(.92); }
    .ama-footer-bottom { display: flex; justify-content: space-between; gap: 24px; padding-block: 20px 24px; border-top: 1px solid rgba(255,255,255,.22); font-size: 13px; text-transform: uppercase; }
    .ama-footer-bottom a { color: inherit; }
    @media (max-width: 1024px) {
        .ama-footer-grid { grid-template-columns: repeat(2, minmax(0,1fr)); gap: 42px; }
    }
    @media (max-width: 640px) {
        .ama-footer-container { width: min(calc(100% - 40px), 1200px); }
        .ama-footer-grid { grid-template-columns: 1fr; gap: 38px; padding-block: 48px 38px; }
        .ama-footer-title { font-size: 28px; }
        .ama-footer-bottom { flex-direction: column; padding-block: 18px 24px; line-height: 1.5; }
    }
</style>

<footer class="ama-footer">
    <div class="ama-footer-container">
        <div class="ama-footer-grid">
            <section>
                <a href="{{ route('home') }}" aria-label="{{ $siteName }} home">
                    @if($siteLogoMedia ?? null)
                        <x-responsive-img :media="$siteLogoMedia" sizes="190px" :fallback-alt="$siteName" class="ama-footer-logo" />
                    @elseif($siteLogo ?? null)
                        <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="ama-footer-logo">
                    @else
                        <span class="font-display text-3xl">{{ $siteName }}</span>
                    @endif
                </a>
                <p class="ama-footer-description">AmaTierra has one of the best yoga retreats in Costa Rica with its inspiring views from the magnificent open-air studio.</p>
            </section>

            <section>
                <h2 class="ama-footer-title">Members</h2>
                <ul class="ama-footer-list">
                    <li>Sustainable Tourism Costa Rica</li>
                    <li>Instituto Costarricense de Turismo</li>
                    <li>American Herbalist Guild</li>
                    <li>American Botanical Council</li>
                    <li>Cámara Costarricense de Hoteles</li>
                    <li>Adventure Hotels of Costa Rica</li>
                </ul>
            </section>

            <section>
                <h2 class="ama-footer-title">Contact us</h2>
                <ul class="ama-footer-list ama-footer-contact">
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.62 10.79a15.7 15.7 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24c1.12.37 2.33.57 3.57.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1.02l-2.2 2.2Z"/></svg><a href="tel:+18666593805">USA Toll Free: 1-866-659-3805</a></li>
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.62 10.79a15.7 15.7 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24c1.12.37 2.33.57 3.57.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1.02l-2.2 2.2Z"/></svg><a href="tel:+50624190110">Costa Rica: 011-506-2419-0110</a></li>
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h16a2 2 0 0 0 2-2V6c0-1.1-.9-2-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/></svg><a href="mailto:amatierra@gmail.com">amatierra@gmail.com</a></li>
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg><span>2 Km Este de la Iglesia Católica de San Pablo, Turrubares, San José, Costa Rica.</span></li>
                </ul>
            </section>

            <div class="ama-footer-map">
                <iframe title="AmaTierra location" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=AmaTierra%20Retreat%20and%20Wellness%20Center%20Costa%20Rica&output=embed"></iframe>
            </div>
        </div>

        <div class="ama-footer-bottom">
            <p>&copy; {{ date('Y') }} AmaTierra Yoga and Wellness Retreat, Costa Rica.</p>
            <a href="{{ url('/terms-and-policies') }}">Terms and Policies</a>
        </div>
    </div>
</footer>
