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
        @if(empty($item->type->code)||$item->type->code!='cart')
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
            <div class="languageBox_show" style="background:url('{{ Storage::url('images/svg/icon_flag_'.$itemSeo->language.'.png') }}') no-repeat center;background-size:100% 100%;"></div>
            @if(!empty($item->seos)&&$item->seos->isNotEmpty())
                <div class="languageBox_list">
                    @foreach($item->seos as $seo)
                        @php
                            $selected = null;
                            if($seo->infoSeo->language==$language) $selected = 'selected';
                            $queryString = !empty(request()->getQueryString()) ? '?'.request()->getQueryString() : '';
                        @endphp
                        <a href="/{{ $seo->infoSeo->slug_full.$queryString }}" class="languageBox_list_item {{ $selected }}" aria-label="{{ $seo->infoSeo->title }}">
                            <div>{{ strtoupper($seo->infoSeo->language) }}</div>
                            <div class="languageBox_list_item_icon" style="background:url('{{ Storage::url('images/svg/icon_flag_'.$seo->infoSeo->language.'.png') }}') no-repeat center;background-size:100% 100%;"></div>
                        </a>
                    @endforeach
                </div>
            @endif
            <div class="languageBox_background"></div>
        </div>
        <div class="iconMenuMobile show-1023" onClick="toggleMenuMobile('js_toggleMenuMobile');">
            <i class="fa-regular fa-bars"></i>
        </div>
    </div>
</div>