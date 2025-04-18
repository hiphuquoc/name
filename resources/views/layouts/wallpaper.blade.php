<!DOCTYPE html>
<html lang="{{ $language ?? 'vi' }}" dir="{{ config('language.'.$language.'.dir') }}" class="{{ request()->cookie('view_mode') ?? config('main_'.env('APP_NAME').'.view_mode')[0]['key'] }}">    

<!-- === START:: Head === -->
<head>
    @include('wallpaper.snippets.head')
</head>
<!-- === END:: Head === -->

<!-- === START:: Body === -->
<body class="background">

    <!-- SVG icon inline -->
    @include('wallpaper.snippets.svgSprite')
    
    <div id="js_openCloseModal_blur">
        {{-- <!-- header Top -->
        <div class="menuTopBackground layoutHeaderTop">
            <div class="layoutHeaderTop_header container">
                <a href="/{{ config('language.'.$language.'.key') }}" class="logoMain" aria-label="{{ config('data_language_1.'.$language.'.home') }} wallsora.com">
                    @if(Route::is('main.home'))
                        <h1 style="opacity:0;">{{ config('data_language_1.'.$language.'.home').' '.config('main_'.env('APP_NAME').'.company_name') }}</h1>
                    @endif
                </a>
            </div>
            <div class="layoutHeaderTop_content container">
                @include('wallpaper.snippets.headerTop')
            </div>
        </div> --}}

        @include('wallpaper.snippets.headerTop')

        <!-- === START:: Content === -->
        <div id="js_settingCollapsedMenu" class="layoutHeaderSide">
            <!-- giữ chỗ thanh bên để chống nhảy trang -->
            <div class="layoutHeaderSide_placeholder"></div>
            <!-- thanh bên -->
            <div id="js_toggleMenuMobile" class="layoutHeaderSide_header container">
                @include('wallpaper.snippets.headerSide')
            </div>
            <div class="backgroundBlurMobileMenu" onClick="toggleMenuMobile('js_toggleMenuMobile');"></div>
            <!-- nội dung chính -->
            <div class="layoutHeaderSide_content container">
                <div id="js_blurBackground">
                    @yield('content')
                    @include('wallpaper.snippets.footer')
                </div>
            </div>
            {{-- <div class="backgroundBlurMobileMenu"></div> --}}
        </div>

        <!-- === START:: Footer === -->
        {{-- @if(!Route::is('main.cart')&&!Route::is('main.confirm'))
            @include('wallpaper.snippets.footer')
        @endif --}}
        {{-- @include('wallpaper.snippets.footer') --}}
        <!-- === END:: Footer === -->

        <div class="bottom">
            <div id="smoothScrollToTop" class="gotoTop" onclick="javascript:smoothScrollToTop();">
                <svg><use xlink:href="#icon_arrow_up"></use></svg>
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