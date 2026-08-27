{{-- View-level styles also work on servers without a frontend build runtime. --}}
<style>
    .ama-home-container { width: min(calc(100% - 40px), 1200px); margin-inline: auto; }
    @media (min-width: 640px) { .ama-home-container { width: min(calc(100% - 64px), 1200px); } }
    #home-hero.ama-clean-hero { position: relative; height: min(76svh, 860px); min-height: 420px; overflow: hidden; background: #1a2a1c; padding: 0; }
    .ama-clean-hero .ama-hero-media { position: absolute; inset: 0; }
    .ama-clean-hero iframe { position: absolute; left: 50%; top: 50%; width: max(100vw, 135.12svh); height: max(56.25vw, 76svh); min-width: 746.67px; min-height: 420px; transform: translate(-50%, -50%); border: 0; pointer-events: none; }
    #home-intro.ama-clean-intro { background: #faf8f3; color: #233e29; padding: 72px 0; text-align: center; }
    .ama-clean-intro h1 { color: #233e29; font-size: clamp(38px, 4.6vw, 64px); line-height: 1.12; margin: 0; }
    .ama-clean-intro p { max-width: 760px; margin: 26px auto 0; font-size: 19px; line-height: 1.75; color: #4c584c; }
    .ama-clean-audience { background: #faf8f3; color: #233e29; padding-bottom: 64px; }
    .ama-audience-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 40px; }
    .ama-audience-grid article { min-width: 0; }
    .ama-audience-photo { aspect-ratio: 4 / 3; overflow: hidden; background: #e8e0d0; }
    .ama-audience-photo img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .ama-clean-audience .ama-audience-content { padding: 32px 0 0; min-height: 0; }
    .ama-clean-audience .ama-audience-content h2 { color: #233e29; font-size: clamp(36px, 3.5vw, 48px); line-height: 1.15; }
    .ama-clean-audience .ama-audience-content p { color: #4c584c; font-size: 18px; line-height: 1.75; }
    .ama-clean-audience .ama-audience-content p:first-child { color: #537039; font-size: 12px; }
    .ama-clean-audience .ama-audience-content a { color: #233e29; border-color: #71805b; font-size: 12px; letter-spacing: .08em; }
    .ama-clean-audience .ama-audience-content a:hover { background: #233e29; color: #fff; }
    /* Staging also has an offers banner: keep its photo separate from its copy. */
    #home-offers-banner { padding-inline: 0; }
    #home-offers-banner > div { width: min(calc(100% - 64px), 1200px); margin-inline: auto; }
    #home-offers-banner > div > div { min-height: 0; background: transparent; border-radius: 0; box-shadow: none; }
    #home-offers-banner > div > div > div.absolute { position: relative; aspect-ratio: 16 / 7; }
    #home-offers-banner > div > div > div.absolute > div { display: none; }
    #home-offers-banner > div > div > div.relative { max-width: none; padding: 32px 0; }
    #home-offers-banner h2, #home-offers-banner h2 span, #home-offers-banner ul { color: #233e29; }
    #home-offers-banner h2 { font-size: clamp(34px, 4vw, 48px); }
    #home-offers-banner h2 span { display: inline; }
    #home-offers-banner li > span:first-child { background: #71805b; }
    @media (max-width: 767px) {
        #home-offers .ama-offers-layout { grid-template-columns: minmax(0, 1fr); }
        #home-offers .ama-offers-copy-content,
        #home-offers .ama-offers-carousel-wrap { min-width: 0; width: 100%; max-width: 100%; }
        #home-hero.ama-clean-hero { height: 60svh; min-height: 320px; }
        .ama-clean-hero iframe { width: max(100vw, 106.67svh); height: max(56.25vw, 60svh); min-width: 568.89px; min-height: 320px; }
        #home-intro.ama-clean-intro { padding: 48px 0; }
        .ama-clean-intro p { font-size: 17px; }
        .ama-audience-grid { grid-template-columns: 1fr; gap: 48px; }
    }
    @media (max-width: 639px) {
        #home-offers-banner > div { width: calc(100% - 40px); }
    }
</style>
