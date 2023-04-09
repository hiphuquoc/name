
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
@if(Route::is('main.confirm'))
    <meta name="robots" content="noindex,nofollow">
@else 
    <meta name="robots" content="index,follow">
@endif
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="fragment" content="!" />
{{-- <link rel="shortcut icon" href="/storage/images/upload/hoaanhtuc-favicon-type-manager-upload.webp" type="image/x-icon"> --}}
<!-- BEGIN: Custom CSS-->
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('/sources/main/ring.css?'.time()) }}">
<link rel="stylesheet" type="text/css" href="{{ asset('/sources/main/loading.css?'.time()) }}"> --}}

@vite(['resources/sources/main/style.scss'])
<!-- END: Custom CSS-->

<!-- BEGIN: FONT AWESOME -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
{{-- <link href="/your-path-to-uicons/css/uicons-rounded-bold.css" rel="stylesheet"> --}}
<!-- END: FONT AWESOME -->

<style type="text/css">
    /* font */
    @font-face{
        font-family:'SVN-Gilroy Bold';
        font-style:normal;
        font-weight:700;
        src:url("/fonts/svn-gilroy_semibold.ttf")
    }
    @font-face{
        font-family:'SVN-Gilroy';
        font-style:normal;
        font-weight:500;
        src:url("/fonts/svn-gilroy_medium.ttf")
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