<!DOCTYPE html>
<html lang="vi">

<!-- === START:: Head === -->
<head>
    @include('main.snippets.head')
</head>
<!-- === END:: Head === -->

<!-- === START:: Body === -->
<body class="background">
    <div id="js_openCloseModal_blur">
        <!-- header Top -->
        <div class="menuTopBackground layoutHeaderSide">
            <div class="layoutHeaderSide_header">
                <div class="logoMain"></div>
            </div>
            <div class="layoutHeaderSide_content">
                <div class="container" style="height:100%;">
                    @include('wallpaper.snippets.headerTop')
                </div>
            </div>
        </div>
        <!-- === START:: Content === -->
        <div class="layoutHeaderSide" style="min-height:calc(100vh - 95px);">
            <div id="js_toggleMenuMobile" class="layoutHeaderSide_header">
                <!-- === START:: Header side === -->
                @include('wallpaper.cacheHTML.create', [
                    'content'   => 'wallpaper.snippets.headerSide'
                ])
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
            @include('main.cacheHTML.create', [
                'content'   => 'main.snippets.footer'
            ])
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
    
    <!-- === START:: Scripts Default === -->
    @include('main.snippets.scriptDefault')
    <!-- === END:: Scripts Default === -->

    <!-- === START:: Scripts Custom === -->
    @stack('scriptCustom')
    <!-- === END:: Scripts Custom === -->
</body>
<!-- === END:: Body === -->

</html>