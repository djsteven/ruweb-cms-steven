{{-- View-level styles also work on servers without a frontend build runtime. --}}
<style>
    .ama-home-container { width: min(calc(100% - 40px), 1200px); margin-inline: auto; }
    @media (min-width: 640px) { .ama-home-container { width: min(calc(100% - 64px), 1200px); } }
    #home-hero.ama-clean-hero { position: relative; height: min(76svh, 860px); min-height: 420px; overflow: hidden; background: #1a2a1c; padding: 0; }
    .ama-clean-hero .ama-hero-media { position: absolute; inset: 0; }
    .ama-clean-hero iframe { position: absolute; left: 50%; top: 50%; width: max(100vw, 135.12svh); height: max(56.25vw, 76svh); min-width: 746.67px; min-height: 420px; translate: none; transform: translate(-50%, -50%); border: 0; pointer-events: none; }
    #home-intro.ama-clean-intro { background: #1a2a1c; color: #f0ebe0; padding: 72px 0; text-align: center; }
    .ama-clean-intro h1 { color: #e8d5a8; font-size: clamp(38px, 4.6vw, 64px); line-height: 1.12; margin: 0; }
    .ama-clean-intro p { max-width: 760px; margin: 26px auto 0; font-size: 19px; line-height: 1.75; color: #f0ebe0; }
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
    /* Editorial cards: clean photography, generous spacing, aligned actions. */
    .ama-clean-audience { background: #f3f0e7; padding: 72px 0 80px; }
    .ama-audience-grid article { display: flex; flex-direction: column; padding: 14px; background: #fffdf8; border: 1px solid #e0e3d7; border-radius: 28px; box-shadow: 0 16px 44px rgba(35,62,41,.08); }
    .ama-audience-photo { aspect-ratio: 8 / 5; border-radius: 18px; }
    .ama-clean-audience .ama-audience-content { padding: 30px 22px 22px; flex: 1; display: flex; }
    .ama-clean-audience .ama-audience-content > div { display: flex; flex-direction: column; width: 100%; }
    .ama-clean-audience .ama-audience-content > div > div:last-child { margin-top: auto; padding-top: 28px; gap: 12px; }
    .ama-clean-audience .ama-audience-content p:first-child { color: #637343; letter-spacing: .16em; margin-bottom: 16px; }
    .ama-clean-audience .ama-audience-content a { color: #fff; background: #2d4a2f; border-color: #2d4a2f; border-radius: 999px; min-height: 48px; padding: 14px 22px; }
    .ama-clean-audience .ama-audience-content a:not(:first-child) { background: transparent; color: #2d4a2f; border-color: #bcc5ac; }
    .ama-clean-audience .ama-audience-content a:first-child::after { content: '→'; margin-left: 14px; font-size: 18px; }
    .ama-clean-audience .ama-audience-content a:hover { background: #1a2a1c; border-color: #1a2a1c; color: #fff; }
    /* Preserve the original photo-background banner within the shared content width. */
    #home-offers-banner { padding-inline: 0; }
    #home-offers-banner > div { width: min(calc(100% - 64px), 1200px); margin-inline: auto; }
    @media (max-width: 767px) {
        #home-offers .ama-offers-layout { grid-template-columns: minmax(0, 1fr); }
        #home-offers .ama-offers-copy-content,
        #home-offers .ama-offers-carousel-wrap { min-width: 0; width: 100%; max-width: 100%; }
        #home-hero.ama-clean-hero { height: 60svh; min-height: 320px; }
        .ama-clean-hero iframe { width: max(100vw, 106.67svh); height: max(56.25vw, 60svh); min-width: 568.89px; min-height: 320px; }
        #home-intro.ama-clean-intro { padding: 48px 0; }
        .ama-clean-intro p { font-size: 17px; }
        .ama-audience-grid { grid-template-columns: 1fr; gap: 48px; }
        .ama-clean-audience { padding: 40px 0 48px; }
        .ama-clean-audience .ama-audience-content { padding: 26px 10px 14px; }
    }
    @media (max-width: 639px) {
        #home-offers-banner > div { width: calc(100% - 40px); }
    }
</style>
