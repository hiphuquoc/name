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
        @if(!Route::is('main.cart')&&!Route::is('main.enCart'))
            {{-- @include('wallpaper.template.search') --}}
            <div id="js_viewSortCart_idWrite">
                @include('wallpaper.cart.cartSort', ['products' => null])
            </div>
        @endif
        <!-- button đăng nhập desktop -->
        <div id="js_checkLoginAndSetShow_button" class="hide-1023" style="height:100%;display:none !important;">
            <!-- tải ajax checkLoginAndSetShow() -->
        </div>
        <!-- language -->
        <div class="languageBox">
            <input type="hidden" id="language" name="language" value="{{ $language ?? '' }}" />
            @if(empty($language)||$language=='vi')
                <div class="languageBox_show" style="background:url('{{ Storage::url('images/svg/icon-vi.png') }}') no-repeat center;background-size:100% 100%;"></div>
            @else
                <div class="languageBox_show" style="background:url('{{ Storage::url('images/svg/icon-en.png') }}') no-repeat center;background-size:100% 100%;"></div>
            @endif
            <div class="languageBox_list">
                @php
                    $urlVi = !empty($item->seo)&&$item->seo->slug_full!='/' ? $item->seo->slug_full : null;
                    $urlEn = !empty($item->en_seo) ? $item->en_seo->slug_full : null;
                @endphp
                <a href="/{{ $urlVi }}" class="languageBox_list_item {{ empty($language)||$language=='vi' ? 'selected' : null }}" aria-label="Xem Name.com.vn bằng ngôn ngữ tiếng Việt">
                    <div>VI</div>
                    <div class="languageBox_list_item_icon" style="background:url('{{ Storage::url('images/svg/icon-vi.png') }}') no-repeat center;background-size:100% 100%;"></div>
                </a>  
                <a href="/{{ $urlEn }}" class="languageBox_list_item {{ !empty($language)&&$language=='en' ? 'selected' : null }}" aria-label="Xem Name.com.vn bằng ngôn ngữ tiếng Anh">
                    <div>EN</div>  
                    <div class="languageBox_list_item_icon" style="background:url('{{ Storage::url('images/svg/icon-en.png') }}') no-repeat center;background-size:100% 100%;"></div>
                </a>
            </div>
            <div class="languageBox_background"></div>
        </div>
        {{-- <div class="settingViewBox hide-1023">
            <i class="fa-solid fa-eye"></i>
        </div> --}}
        <div class="iconMenuMobile show-1023" onClick="toggleMenuMobile('js_toggleMenuMobile');">
            <i class="fa-regular fa-bars"></i>
        </div>
    </div>
</div>