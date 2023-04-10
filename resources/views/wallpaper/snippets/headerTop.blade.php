<div class="menuTop">
    <!-- logo mobile -->
    <div class="menuTop_item">
        <div class="logoMain"></div>
    </div>

    <!-- breadcrumb desktop -->
    <div class="menuTop_item">
        <div class="hide-1199">
            @include('wallpaper.template.breadcrumb')
        </div>
    </div>
    <div class="menuTop_item rightBox">
        @if(!Route::is('main.cart'))
            
            

            <div id="js_viewSortCart_idWrite">
                @include('wallpaper.cart.cartSort', ['products' => null])
            </div>
        @endif
        <!-- button đăng nhập desktop -->
        <div id="js_checkLoginAndSetShow_button" class="hide-1023" style="height:100%;display:none !important;">
            <!-- tải ajax checkLoginAndSetShow() -->
        </div>
        {{-- <div class="settingViewBox hide-1023">
            <i class="fa-solid fa-eye"></i>
        </div> --}}
        <div class="iconMenuMobile show-1023" onClick="toggleMenuMobile('js_toggleMenuMobile');">
            <i class="fa-regular fa-bars"></i>
        </div>
    </div>
</div>