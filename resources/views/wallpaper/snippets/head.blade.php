
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
@if(Route::is('main.confirm'))
    <meta name="robots" content="noindex,nofollow">
@else
    @if(!empty($index)&&$index=='no')
        <meta name="robots" content="noindex,nofollow">
    @else 
        <meta name="robots" content="index,follow">
    @endif
@endif
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="fragment" content="!" />
<link rel="shortcut icon" href="/storage/images/upload/logo-type-manager-upload.webp" type="image/x-icon">
<!-- BEGIN: Custom CSS-->
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('/sources/main/ring.css?'.time()) }}">
<link rel="stylesheet" type="text/css" href="{{ asset('/sources/main/loading.css?'.time()) }}"> --}}

<!-- BEGIN: FONT AWESOME -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
{{-- <link href="/your-path-to-uicons/css/uicons-rounded-bold.css" rel="stylesheet"> --}}
<!-- END: FONT AWESOME -->

<style type="text/css">
    /* @font-face {
        font-family: 'Segoe-UI Light';
        font-style: normal;
        font-weight: 400;
        src: url('/fonts/SegoeUI-Light.ttf');
        font-display: swap;
    } */

    @font-face {
        font-family: 'Segoe-UI';
        font-style: normal;
        font-weight: 500;
        src: url('/fonts/SegoeUI.ttf');
        font-display: swap;
    }

    @font-face {
        font-family: 'Segoe-UI Semi';
        font-style: normal;
        font-weight: 700;
        font-display: swap;
        src: url('/fonts/SegoeUI-SemiBold.ttf');
    }

    @font-face {
        font-family: 'Segoe-UI Bold';
        font-style: normal;
        font-weight: 700;
        font-display: swap;
        src: url('/fonts/SegoeUI-Bold.ttf');
    }

    /* @font-face {
        font-family: 'SVN-Gilroy Thin';
        font-style: normal;
        font-weight: 400;
        src: url(//bizweb.dktcdn.net/100/438/408/themes/919724/assets/svn-gilroy_regular.ttf?1698220622470);
        font-display: 'Swap';
    }

    @font-face {
        font-family: 'SVN-Gilroy Light';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url(//bizweb.dktcdn.net/100/438/408/themes/919724/assets/svn-gilroy_regular.ttf?1698220622470);
    } */

    @font-face {
        font-family: 'SVN-Gilroy';
        font-style: normal;
        font-display: swap;
        font-weight: 500;
        src: url('/fonts/svn-gilroy_medium.ttf');
    }

    @font-face {
        font-family: 'SVN-Gilroy Med';
        font-style: normal;
        font-display: swap;
        font-weight: 700;
        src: url('/fonts/svn-gilroy_med.ttf');
    }

    @font-face {
        font-family: 'SVN-Gilroy Semi';
        font-style: normal;
        font-weight: 700;
        font-display: swap;
        src: url('/fonts/svn-gilroy_semibold.ttf');
    }

    @font-face {
        font-family: 'SVN-Gilroy Bold';
        font-style: normal;
        font-weight: 700;
        font-display: swap;
        src: url('/fonts/svn-gilroy_semibold.ttf');
    }
</style>

@stack('headCustom')

<!-- BEGIN: SLICK -->
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<!-- END: SLICK -->
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/vendors/css/forms/select/select2.min.css') }}"> --}}
<!-- BEGIN: Jquery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<!-- END: Jquery -->

<!-- BEGIN: Google Analytics -->
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-D3XCL5MK23"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-D3XCL5MK23');
</script>
<!-- END: Google Analytics -->
{{-- <!-- Start:: Tiktok Analutics -->
<script>
    !function (w, d, t) {
      w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
    
      ttq.load('CHCTFQ3C77U3VDB5TNNG');
      ttq.page();
    }(window, document, 'ttq');
</script>
<!-- End:: Tiktok Analutics --> --}}

@vite(['resources/sources/main/style.scss'])
<!-- END: Custom CSS-->