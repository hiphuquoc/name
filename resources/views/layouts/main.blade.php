<!DOCTYPE html>
<html lang="vi">

<!-- === START:: Head === -->
<head>
    @include('main.snippets.head')
</head>
<!-- === END:: Head === -->

<!-- === START:: Body === -->
<body>
    <div id="js_openCloseModal_blur">

        <!-- === START:: Content === -->
        @yield('content')

        <!-- === START:: Footer === -->
        @include('main.cacheHTML.create', [
            'content'   => 'main.snippets.footer'
        ])
        <!-- === END:: Footer === -->

        {{-- <div class="bottom">
            <div id="gotoTop" class="gotoTop" onclick="javascript:gotoTop();" style="display: block;">
                <i class="fas fa-chevron-up"></i>
            </div>
            @stack('bottom')
        </div> --}}

        <div class="bottom">
            <div id="smoothScrollToTop" class="gotoTop" onclick="javascript:smoothScrollToTop();" style="display: block;">
                <i class="fas fa-chevron-up"></i>
            </div>
            @stack('bottom')
        </div>
    </div>
    <!-- Message -->
    <div id="js_addToCart_idWrite">
        @include('main.cart.cartMessage', [
            'title'     => $item->name,
            'option'    => null,
            'quantity'  => 0,
            'price'     => 0,
            'image'     => null

        ])
    </div>
    
    <!-- Modal -->
    @stack('modal')
    @include('main.modal.registrySeller')
    @include('main.modal.messageModal')
    <!-- === START:: Scripts Default === -->
    @include('main.snippets.scriptDefault')
    <!-- === END:: Scripts Default === -->

    <!-- === START:: Scripts Custom === -->
    @stack('scriptCustom')
    <!-- === END:: Scripts Custom === -->
</body>
<!-- === END:: Body === -->

</html>