<!DOCTYPE html>
<html lang="{{ $language ?? 'vi' }}">

<!-- === START:: Head === -->
<head>
    @include('wallpaper.snippets.head')
</head>
<!-- === END:: Head === -->

<!-- === START:: Body === -->
<body class="background">
    <div id="js_openCloseModal_blur">
        <!-- header Top -->
        <div class="menuTopBackground layoutHeaderTop">
            <div class="layoutHeaderTop_header">
                @if(!empty($language)&&$language=='en')
                    <a href="/en" class="logoMain">
                        @if(Route::is('main.enHome'))
                            <h1 style="opacity:0;">Home {{ config('main.company_name') }}</h1>
                        @endif
                    </a>
                @else 
                    <a href="/" class="logoMain">
                        @if(Route::is('main.home'))
                            <h1 style="opacity:0;">Trang chủ {{ config('main.company_name') }}</h1>
                        @endif
                    </a>
                @endif
                <!-- search box -->
                @include('wallpaper.template.search')
            </div>
            <div class="layoutHeaderTop_content">
                <div class="container" style="height:100%;">
                    @include('wallpaper.snippets.headerTop')
                </div>
            </div>
        </div>
        <!-- === START:: Content === -->
        <div class="layoutHeaderSide" style="min-height:calc(100vh - 95px);">
            <div id="js_toggleMenuMobile" class="layoutHeaderSide_header">
                <!-- === START:: Header side === -->
                {{-- @include('wallpaper.cacheHTML.create', [
                    'content'  => 'wallpaper.snippets.headerSide'
                ]) --}}
                @include('wallpaper.snippets.headerSide')
                <!-- === END:: Header side === -->
            </div>

            <div class="layoutHeaderSide_content">
                <div id="js_blurBackground">
                    @yield('content')
                </div>
            </div>
            {{-- <div class="backgroundBlurMobileMenu"></div> --}}
        </div>

        <!-- === START:: Footer === -->
        @if(!Route::is('main.cart')&&!Route::is('main.confirm'))
            {{-- @include('wallpaper.cacheHTML.create', [
                'content'   => 'wallpaper.snippets.footer'
            ]) --}}
            @include('wallpaper.snippets.footer')
        @endif
        <!-- === END:: Footer === -->

        <div class="bottom">
            <div id="smoothScrollToTop" class="gotoTop" onclick="javascript:smoothScrollToTop();" style="display: block;">
                <i class="fas fa-chevron-up"></i>
            </div>
            @stack('bottom')
        </div>

    </div>
    
    <!-- Modal -->
    @stack('modal')

    <!-- login form modal -->
    <div id="js_checkLoginAndSetShow_modal">
        <!-- tải ajaax checkLoginAndSetShow() -->
    </div>
    
    <!-- === START:: Scripts Default === -->
    @include('wallpaper.snippets.scriptDefault')
    <!-- === END:: Scripts Default === -->

    <!-- === START:: Scripts Custom === -->
    @stack('scriptCustom')
    <!-- === END:: Scripts Custom === -->
</body>
<!-- === END:: Body === -->

</html>