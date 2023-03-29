<div class="menuTop">
    <!-- breadcrumb desktop -->
    <div class="menuTop_item">
        <div class="hide-1199">
            @include('wallpaper.template.breadcrumb')
        </div>
    </div>
    <!-- logo mobile -->
    <div class="menuTop_item show-1023" style="margin-left:0;">
        <div class="logoMain"></div>
    </div>
    <div class="menuTop_item rightBox">
        <div class="show-1023">
            <div class="buttonSearchMobile" onClick="toggleSearchMobile();">
                <img type="submit" src="{{ Storage::url('images/svg/search.svg') }}" alt="tìm kiếm hình nền điện thoại" title="tìm kiếm hình nền điện thoại" />
            </div>
        </div>
        @if(!Route::is('main.cart'))
            @include('wallpaper.template.search')
            <div id="js_viewSortCart_idWrite">
                @include('wallpaper.cart.cartSort', ['products' => null])
            </div>
        @endif
        <!-- button đăng nhập desktop -->
        <div class="hide-1023" style="height:100%;">
            <div class="loginBox"> 
                <img src="{{ Storage::url('images/svg/icon-user.svg') }}" alt="đăng nhập {{ config('main.company_name') }}" title="đăng nhập {{ config('main.company_name') }}" />
                <div>Đăng nhập</div>
            </div>
        </div>
        <div class="settingViewBox hide-1023">
            <i class="fa-solid fa-eye"></i>
        </div>
        <div class="iconMenuMobile show-1023" onClick="toggleMenuMobile('js_toggleMenuMobile');">
            <i class="fa-regular fa-bars"></i>
        </div>
    </div>
</div>