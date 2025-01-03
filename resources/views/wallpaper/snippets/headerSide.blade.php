@php
    /* trang ve-chung-toi */
    $pageAboutUs = \App\Models\Page::select('*')
                    ->whereHas('seo', function($query){
                        $query->where('slug', 've-chung-toi');
                    })
                    ->with('seo', 'seos')
                    ->first();
    /* chủ đề */
    $wallpaperMobile            = [];
    $tmp                        = \App\Models\Category::getTreeCategory();
    foreach($tmp as $categoryLv1){
        if($categoryLv1->seo->level==1){
            $wallpaperMobile    = $categoryLv1;
            break;
        }
    }
    /* trang chính sách */
    $policies                   = \App\Models\Page::select('page_info.*')
                                    ->join('seo', 'seo.id', '=', 'page_info.seo_id')
                                    ->whereHas('type', function($query){
                                        $query->where('code', 'policy');
                                    })
                                    ->orderBy('seo.ordering', 'DESC')
                                    ->orderBy('seo.id', 'ASC')
                                    ->with('seo', 'seos')
                                    ->get();
    /* chuyên mục blog */
    $categoriesBlog             = \App\Models\CategoryBlog::getTreeCategory();
    /* url hiện tại */
    $urlPath                    = urldecode(request()->path());
@endphp             
<div class="logoInMenuMobile show-991">
    <a href="/{{ $language }}" class="logoMain" aria-label="{{ config('language.'.$language.'.data.home') }} Name.com.vn"></a>
</div>
<!-- icon hiển thị chế độ xem -->
<div class="layoutHeaderSide_header_menuView" onclick="settingCollapsedMenu();">
    @php
        $icon = file_get_contents('storage/images/svg/icon-settingView.svg');
    @endphp
    {!! $icon !!}
</div>
<div class="headerSide customScrollBar-y">
    <ul>
        <!-- trang chủ -->
        @php
            $icon           = file_get_contents('storage/images/svg/icon-home-1.svg');
            $selected       = '';
            // if($urlPath==$language||$urlPath=='/') $selected = 'selected';
        @endphp
        <li class="{{ $selected }}">
            <a href="/{{ $language }}" title="{{ config('language.'.$language.'.data.home').' '.config('main_'.env('APP_NAME').'.info.'.env('APP_NAME').'.company_name') }}" aria-label="{{ config('language.'.$language.'.data.home') }} Name.com.vn">
                {!! $icon !!}
                <div class="maxLine_1">{{ config('language.'.$language.'.data.home') }}</div>
            </a>
        </li>
        <!-- về chúng tôi -->
        @php
            $icon       = file_get_contents('storage/images/svg/icon-about-me-2.svg');
            $urlAbotUs      = '';
            $nameAboutUs    = '';
            $selected       = '';
            foreach($pageAboutUs->seos as $s){
                if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language){
                    $urlAbotUs      = $s->infoSeo->slug_full;
                    $nameAboutUs    = $s->infoSeo->title;
                    if($urlAbotUs==$urlPath) $selected = 'selected';
                }
            }
        @endphp
        <li class="{{ $selected }}">
            <a href="/{{ $urlAbotUs }}" title="{{ $nameAboutUs }}" aria-label="{{ $nameAboutUs }}">
                {!! $icon !!}
                <div class="maxLine_1">{{ $nameAboutUs }}</div>
            </a>
        </li>
        <!-- chủ đề -->
        @if(!empty($wallpaperMobile))
            @php
                $titlePhoneWallpaper = config('language.'.$language.'.data.wallpaper_theme.'.env('APP_NAME'));
                $url      = '';
                foreach($wallpaperMobile->seos as $s){
                    if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language){
                        $url    = $s->infoSeo->slug_full;
                        break;
                    }
                }
                $classTmp       = 'close';
                $classActive    = '';
                $iconRight      = '<i class="fa-solid fa-plus"></i>';
                $selected       = '';
                $flagOpen       = $url==$urlPath ? true : false;
                if($flagOpen==true){
                    $classTmp       = 'open';
                    $classActive    = 'active';
                    $iconRight      = '<i class="fa-solid fa-minus"></i>';
                    $selected       = 'selected';
                }
                $icon = file_get_contents('storage/images/svg/icon-category-2.svg');
            @endphp
            <li class="{{ $selected }}">
                <div class="{{ $classTmp }}" onclick="showHideListMenuMobile(this, '{{ $url }}')">
                    {!! $icon !!}
                    @if($flagOpen==true)
                        <div class="maxLine_1">{{ $titlePhoneWallpaper }}</div>
                    @else 
                        <a href="{{ env('APP_URL') }}/{{ $url }}" class="maxLine_1" arira-label="{{ $wallpaperMobile->name }}">{{ $titlePhoneWallpaper }}</a>
                    @endif
                    {!! $iconRight !!}
                </div>
                <ul id="{{ $url }}" class="filterLinkSelected {{ $classActive }}">
                    @foreach($wallpaperMobile->childs as $event)
                        @if(!empty($event->seo->type)&&$event->seo->type=='category_info')
                            @foreach($event->seos as $seo)
                                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                                    @php
                                        $title      = $seo->infoSeo->title ?? null;
                                        $urlFull    = env('APP_URL').'/'.$seo->infoSeo->slug_full;
                                    @endphp
                                    <li>
                                        <a href="{{ $urlFull }}" title="{{ $title }}" aria-label="{{ $title }}">
                                            <div class="maxLine_1">{{ $title }} {!! $event->products->count()>0 ? '(<span class="highLight">'.$event->products->count().'</span>)' : null !!}</div>
                                        </a>
                                    </li>
                                    @break
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </ul>
            </li>
        @endif
        <!-- phong cách -->
        <li>
            @php
                $icon = file_get_contents('storage/images/svg/icon-brush-1.svg');
            @endphp
            <div class="open" onclick="showHideListMenuMobile(this, 'phong-cach')">
                {!! $icon !!}
                <div class="maxLine_1" style="margin-left:-3px;">{{ config('language.'.$language.'.data.wallpaper_style.'.env('APP_NAME')) }}</div>
                <i class="fa-solid fa-plus"></i>
            </div>
            <ul id="phong-cach" class="filterLinkSelected">
                @foreach($wallpaperMobile->childs as $event)
                    @if(!empty($event->seo->type)&&$event->seo->type=='style_info')
                        @foreach($event->seos as $seo)
                            @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                                @php
                                    $title      = $seo->infoSeo->title ?? null;
                                    $urlFull    = env('APP_URL').'/'.$seo->infoSeo->slug_full;
                                @endphp
                                <li>
                                    <a href="{{ $urlFull }}" title="{{ $title }}" aria-label="{{ $title }}">
                                        <div class="maxLine_1">{{ $title }} {!! $event->products->count()>0 ? '(<span class="highLight">'.$event->products->count().'</span>)' : null !!}</div>
                                    </a>
                                </li>
                                @break
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </li>
        <!-- sự kiện -->
        <li>
            @php
                $altPhoneWallpaperEvent = config('language.'.$language.'.data.phone_wallpaper.'.env('APP_NAME'));
            @endphp
            <div class="close" onclick="showHideListMenuMobile(this, 'su-kien')">
                <img src="{{ Storage::url('images/svg/icon-event-1.png') }}" alt="{!! $altPhoneWallpaperEvent !!}" title="{!! $altPhoneWallpaperEvent !!}" />
                <div class="maxLine_1">{{ config('language.'.$language.'.data.event') }}</div>
                <i class="fa-solid fa-plus"></i>
            </div>
            <ul id="su-kien" class="filterLinkSelected">
                @foreach($wallpaperMobile->childs as $event)
                    @if(!empty($event->seo->type)&&$event->seo->type=='event_info')
                        @foreach($event->seos as $seo)
                            @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                                @php
                                    $title      = $seo->infoSeo->title ?? null;
                                    $urlFull    = env('APP_URL').'/'.$seo->infoSeo->slug_full;
                                @endphp
                                <li>
                                    <a href="{{ $urlFull }}" title="{{ $title }}" aria-label="{{ $title }}">
                                        <div class="maxLine_1">{{ $title }} {!! $event->products->count()>0 ? '(<span class="highLight">'.$event->products->count().'</span>)' : null !!}</div>
                                    </a>
                                </li>
                                @break
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </li>
        <!-- miễn phí -->
        <li>
            @php
                $icon                   = file_get_contents('storage/images/svg/icon-share-1.svg');
                $wallpaperFreeText      = config('language.'.$language.'.data.free_wallpaper.'.env('APP_NAME'));
                $slugFullWallpaperFree  = '';
                foreach($wallpaperMobile->childs as $child){
                    if(in_array($child->seo->slug, config('main_'.env('APP_NAME').'.url_free_wallpaper_category'))){
                        foreach($child->seos as $seo){
                            if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                                $slugFullWallpaperFree = $seo->infoSeo->slug_full;
                                break;
                            }
                        }
                    }
                }
            @endphp
            <a href="{{ env('APP_URL') }}/{{ $slugFullWallpaperFree }}" title="{{ $wallpaperFreeText }}" aria-label="{{ $wallpaperFreeText }}">
                {!! $icon !!}
                <div class="maxLine_1">{{ $wallpaperFreeText }}</div>
            </a>
        </li>
        <!-- hỗ trợ -->
        <li>
            <div class="close" onclick="showHideListMenuMobile(this, 'ho-tro')">
                @php
                    $icon = file_get_contents('storage/images/svg/icon-support-1.svg');
                @endphp
                {!! $icon !!}
                <div class="maxLine_1">{{ config('language.'.$language.'.data.support') }}</div>
                <i class="fa-solid fa-plus"></i>
            </div>
            <ul id="ho-tro" class="filterLinkSelected">
                @foreach($policies as $policy)
                    @php
                        $title      = '';
                        $slugPage   = '';
                        foreach($policy->seos as $seo){
                            if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                                $title      = $seo->infoSeo->title;
                                $slugPage   = $seo->infoSeo->slug_full;
                                break;
                            }
                        }
                    @endphp
                    <li>
                        <a href="{{ env('APP_URL').'/'.$slugPage }}" title="{{ $title }}" aria-label="{{ $title }}">
                            <div class="maxLine_1">{{ $title }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
        <!-- chủ đề blog => duyệt để in tất cả cấp 1 -->
        @foreach($categoriesBlog as $categoryBlogLv1)
            @php
                $cateogyrBlogLv1ByLanguage = [];
                foreach($categoryBlogLv1->seos as $seo){
                    if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                        $cateogyrBlogLv1ByLanguage = $seo->infoSeo;
                        break;
                    }
                }
                $idGroupMenu    = $cateogyrBlogLv1ByLanguage->slug ?? '';
                $classTmp       = 'close';
                $classActive    = '';
                $iconRight      = '<i class="fa-solid fa-plus"></i>';
                $selected       = '';
                $flagOpen       = $idGroupMenu==$urlPath ? true : false;
                if($flagOpen==true){
                    $classTmp       = 'open';
                    $classActive    = 'active';
                    $iconRight      = '<i class="fa-solid fa-minus"></i>';
                    $selected       = 'selected';
                }
            @endphp
            @if(!empty($cateogyrBlogLv1ByLanguage))
                <li class="{{ $selected }}">
                    <div class="{{ $classTmp }}" onclick="showHideListMenuMobile(this, '{{ $idGroupMenu }}')">
                        @php
                            $icon = file_get_contents('storage/images/svg/icon-blog-1.svg');
                        @endphp
                        {!! $icon !!}
                        {{-- <div class="maxLine_1">{{ $cateogyrBlogLv1ByLanguage->title }}</div> --}}
                        <a href="{{ env('APP_URL') }}/{{ $cateogyrBlogLv1ByLanguage->slug_full }}" class="maxLine_1" arira-label="{{ $wallpaperMobile->name }}">{{ $cateogyrBlogLv1ByLanguage->title }}</a>
                        {!! $iconRight !!}
                    </div>
                    <ul id="{{ $idGroupMenu }}" class="filterLinkSelected {{ $classActive }}">
                        @foreach($categoryBlogLv1->childs as $categoryBlog)
                            @php
                                $title      = '';
                                $slugPage   = '';
                                foreach($categoryBlog->seos as $seo){
                                    if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                                        $title      = $seo->infoSeo->title;
                                        $slugPage   = $seo->infoSeo->slug_full;
                                        break;
                                    }
                                }
                            @endphp
                            <li>
                                <a href="{{ env('APP_URL').'/'.$slugPage }}" title="{{ $title }}" aria-label="{{ $title }}">
                                    <div class="maxLine_1">{{ $title }}</div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif
        @endforeach
    </ul>
</div>

<div class="socialBox">
    <div class="socialBox_social">
        <div class="socialBox_social_title">
            {{ config('language.'.$language.'.data.connect_with_us') }}
        </div>
        <div class="socialBox_social_box">
            <a href="https://www.facebook.com/wallpapers.name.com.vn" class="socialBox_social_box_item" aria-label="facebook">
                <i class="fa-brands fa-facebook-f"></i>
            </a>
            <a href="https://www.instagram.com/wallpapers_namecomvn" class="socialBox_social_box_item" aria-label="instagram">
                <i class="fa-brands fa-instagram"></i>
            </a>
            <a href="https://www.youtube.com/@wallpapers_namecomvn" class="socialBox_social_box_item" aria-label="youtube">
                <i class="fa-brands fa-youtube"></i>
            </a>
            <a href="https://www.tiktok.com/@wallpapers_namecomvn" class="socialBox_social_box_item" aria-label="tiktok">
                <i class="fa-brands fa-tiktok"></i>
            </a>
            {{-- <a href="https://twitter.com/wallpapers_name" class="socialBox_social_box_item" aria-label="twitter">
                <i class="fa-brands fa-twitter"></i>
            </a> --}}
        </div>
    </div>
    
    <!-- DMCA -->
    @include('wallpaper.template.dmca')

</div>

<div class="closeButtonMobileMenu show-991" onClick="toggleMenuMobile('js_toggleMenuMobile');">
    <i class="fa-sharp fa-solid fa-xmark"></i>
</div>
<div class="backgroundBlurMobileMenu" onClick="toggleMenuMobile('js_toggleMenuMobile');"></div>

@push('scriptCustom')
    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', function() {
            var Url = decodeURIComponent(document.URL);
            $('.headerSide .filterLinkSelected a').each(function(){
                const regex = new RegExp("^" + $(this).attr('href'));
                if(regex.test(Url)) {
                    /* mở thẻ cha chứa phần tử trang hiện tại */
                    $(this).closest('ul').addClass('active');
                    /* mở luôn thẻ chứa các phần tử con của trang hiện tại */
                    $(this).next('ul').addClass('active');
                    $(this).closest('ul').children().each(function(){
                        $(this).removeClass('selected');
                    })
                    
                    $(this).closest('li').addClass('selected');
                    /* thay icon */
                    $(this).closest('ul').closest('li').find('.fa-plus').removeClass('fa-plus').addClass('fa-minus');
                }
            });
            /* tải status collapsed */
            getStatusCollapse();
        });

        function showHideListMenuMobile(element, idMenu){
            let elementMenu     = $('#'+idMenu);
            let flag            = elementMenu.height();
            if(flag<=0){
                elementMenu.addClass('active');
            }else {
                elementMenu.removeClass('active');
            }
            /* toggle icon */
            const elementIcon = $(element).find('i');
            if ($(elementIcon).hasClass('fa-plus')) {
                $(elementIcon).removeClass('fa-plus').addClass('fa-minus');
            } else {
                $(elementIcon).removeClass('fa-minus').addClass('fa-plus');
            }
        }

        function settingCollapsedMenu(){
            const element       = $('#js_settingCollapsedMenu');
            element.find('.layoutHeaderSide_header').css('width', '4.25rem');
            /* xác định hành động */
            var action          = 'on';
            if(element.hasClass('collapsed'))  action = 'off';
            /* thiết lập */
            let dataForm        = {};
            dataForm.action     = action;            
            const queryString   = new URLSearchParams(dataForm).toString();
            fetch('/settingCollapsedMenu?' + queryString, {
                method  : 'GET',
                mode    : 'cors',
            })
            .then(response => {
                if (!response.ok){
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(response => {
                if(action=='on'){
                    element.addClass('collapsed');
                }else {
                    element.removeClass('collapsed');
                }
                setTimeout(() => {
                    element.find('.layoutHeaderSide_header').attr('style', '');
                }, 200);
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }

        function getStatusCollapse() {
            fetch('/getStatusCollapse', {
                method: 'GET',
                mode: 'cors',
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status == 'on') {
                    $('#js_settingCollapsedMenu').addClass('collapsed');
                } else {
                    $('#js_settingCollapsedMenu').removeClass('collapsed');
                }
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }

    </script>
@endpush