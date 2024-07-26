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
            <div class="languageBox_show" onclick="closeLanguageBoxList('ja_closeLanguageBoxList');">
                <i class="fa-solid fa-globe"></i>{{ strtoupper($language) }}
            </div>
            @if(!empty($item->seos)&&$item->seos->isNotEmpty())
                <div id="ja_closeLanguageBoxList" class="languageBox_list">
                    <div class="languageBox_list_close" onclick="closeLanguageBoxList('ja_closeLanguageBoxList');"><i class="fa-sharp fa-solid fa-xmark"></i></div>
                    @foreach(config('language') as $ld)
                        @php
                            $queryString = !empty(request()->getQueryString()) ? '?'.request()->getQueryString() : '';
                            $flagHas = false;
                            foreach($item->seos as $seo){
                                if(!empty($seo->infoSeo)){
                                    if($seo->infoSeo->language==$ld['key']) {
                                        $urlOfPageWithLanguage = $seo->infoSeo->slug_full.$queryString;
                                        $flagHas = true;
                                        break;
                                    }
                                }
                            }
                            /* selected */
                            $selected = null;
                            if($seo->infoSeo->language==$language) $selected = 'selected';
                        @endphp
                        @if($flagHas==true)
                            <a href="{{ $urlOfPageWithLanguage }}" class="languageBox_list_item maxLine_1 {{ $selected }}">{{ $ld['name_by_language'] }}</a>
                        @else 
                            <div class="languageBox_list_item maxLine_1">{{ $ld['name_by_language'] }}</div>
                        @endif
                    @endforeach
                </div>
            @endif
            <div id="ja_closeLanguageBoxList_background" class="languageBox_background"></div>
        </div>
        <div class="iconMenuMobile show-1023" onClick="toggleMenuMobile('js_toggleMenuMobile');">
            <i class="fa-regular fa-bars"></i>
        </div>
    </div>
</div>

@push('scriptCustom')
    <script type="text/javascript">
        function closeLanguageBoxList(idElemt){
            let displayE    = $('#' + idElemt).css('display');
            if(displayE=='none'){
                $('#' + idElemt).css('display', 'flex');
                $('#' + idElemt + '_background').css('display', 'flex');
                $('body').css('overflow', 'hidden');
            }else {
                $('#' + idElemt).css('display', 'none');
                $('#' + idElemt + '_background').css('display', 'none');
                $('body').css('overflow', 'unset');
            }
        }
    </script>
@endpush