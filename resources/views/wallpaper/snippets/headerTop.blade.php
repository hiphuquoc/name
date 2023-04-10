<div class="menuTop">
    
    <!-- breadcrumb desktop -->
    <div class="menuTop_item hide-1023">
        @include('wallpaper.template.breadcrumb')
    </div>

    <div class="menuTop_item rightBox">

        <div class="show-1023">
            <div class="buttonSearchMobile" onClick="toggleSearchMobile();">
                <img type="submit" src="{{ Storage::url('images/svg/search.svg') }}" alt="tìm kiếm hình nền điện thoại" title="tìm kiếm hình nền điện thoại" />
            </div>
        </div>
        @if(!Route::is('main.cart'))
            {{-- @include('wallpaper.template.search') --}}
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