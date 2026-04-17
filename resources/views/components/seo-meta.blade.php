@php
    use App\Helpers\ContentHelper;
    use App\Models\Setting;

    $meta = isset($page) ? $page->meta() : [];
    $siteName = Setting::get('site_name') ?: config('app.name');
    $faviconSetting = Setting::get('site_favicon');
    $googleTagId = Setting::get('google_tag_id');
    $metaPixelId = Setting::get('meta_pixel_id');
    $searchConsoleVerificationToken = Setting::get('search_console_verification_token');
    $favicon = is_string($faviconSetting)
        ? $faviconSetting
        : $faviconSetting?->url();
    $favicon = $favicon ?: '/favicon.ico';

    $title = ContentHelper::metaTitle($meta);
    $description = ContentHelper::metaDescription($meta);
    $image = ContentHelper::metaImage($meta);
    $url = isset($page) ? url($page->url()) : url()->current();
@endphp

<title>{{ $title }}{{ $title !== $siteName ? ' — ' . $siteName : '' }}</title>

@if($favicon)
<link rel="icon" type="image/x-icon" href="{{ $favicon }}">
@endif
<link rel="icon" type="image/png" sizes="32x32" href="/favicon_io/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon_io/favicon-16x16.png">

@if($description)
<meta name="description" content="{{ $description }}">
@endif

@if($searchConsoleVerificationToken)
<meta name="google-site-verification" content="{{ $searchConsoleVerificationToken }}">
@endif

<link rel="canonical" href="{{ $url }}">

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $title }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:site_name" content="{{ $siteName }}">
@if($description)
<meta property="og:description" content="{{ $description }}">
@endif
@if($image)
<meta property="og:image" content="{{ $image }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $image ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $title }}">
@if($description)
<meta name="twitter:description" content="{{ $description }}">
@endif
@if($image)
<meta name="twitter:image" content="{{ $image }}">
@endif

@if($googleTagId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($googleTagId) }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $googleTagId }}');
</script>
@endif

@if($metaPixelId)
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $metaPixelId }}');
    fbq('track', 'PageView');
</script>
@endif
