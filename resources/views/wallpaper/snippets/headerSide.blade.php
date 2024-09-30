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
                                    ->with('seo')
                                    ->get();
@endphp             
<div class="logoInMenuMobile show-1023">
    <a href="/{{ $language }}" class="logoMain" aria-label="{{ config('language.'.$language.'.data.home') }} Name.com.vn"></a>
</div>
<div class="headerSide customScrollBar-y">
    <ul>
        <!-- trang chủ -->
        <li>
            @php
                $icon = file_get_contents('storage/images/svg/icon-home-1.svg');
            @endphp
            <a href="/{{ $language }}" title="{{ config('language.'.$language.'.data.home').' '.config('main_'.env('APP_NAME').'.info.'.env('APP_NAME').'.company_name') }}" aria-label="{{ config('language.'.$language.'.data.home') }} Name.com.vn">
                {!! $icon !!}
                <div class="maxLine_1">{{ config('language.'.$language.'.data.home') }}</div>
            </a>
        </li>
        <!-- về chúng tôi -->
        <li>
            @php
                $icon       = file_get_contents('storage/images/svg/icon-about-me-2.svg');
                $urlAbotUs      = '';
                $nameAboutUs    = '';
                foreach($pageAboutUs->seos as $s){
                    if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language){
                        $urlAbotUs      = $s->infoSeo->slug_full;
                        $nameAboutUs    = $s->infoSeo->title;
                    }
                }
            @endphp
            <a href="/{{ $urlAbotUs }}" title="{{ $nameAboutUs }}" aria-label="{{ $nameAboutUs }}">
                {!! $icon !!}
                <div class="maxLine_1">{{ $nameAboutUs }}</div>
            </a>
        </li>
        <!-- chủ đề -->
        @if(!empty($wallpaperMobile))
            <li>
                @php
                    $titlePhoneWallpaper = config('language.'.$language.'.data.wallpaper_theme.'.env('APP_NAME'));
                    $url      = '';
                    foreach($wallpaperMobile->seos as $s){
                        if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language){
                            $url    = $s->infoSeo->slug_full;
                            break;
                        }
                    }
                    $classTmp   = 'close';
                    $styleTmp   = '';
                    $iconRight  = '<i class="fa-solid fa-plus"></i>';
                    $flagOpen   = env('APP_URL').'/'.$url==urldecode(Request::url()) ? true : false;
                    if($flagOpen==true){
                        $classTmp   = 'open';
                        $styleTmp   = 'style="height:auto;opacity:1;"';
                        $iconRight  = '<i class="fa-solid fa-minus"></i>';
                    }
                    $icon = file_get_contents('storage/images/svg/icon-category-2.svg');
                @endphp
                <div class="{{ $classTmp }}" onclick="showHideListMenuMobile(this, '{{ $url }}')">
                    {!! $icon !!}
                    @if($flagOpen==true)
                        <div class="maxLine_1">{{ $titlePhoneWallpaper }}</div>
                    @else 
                        <a href="{{ env('APP_URL') }}/{{ $url }}" class="maxLine_1" arira-label="{{ $wallpaperMobile->name }}">{{ $titlePhoneWallpaper }}</a>
                    @endif
                    {!! $iconRight !!}
                </div>
                <ul id="{{ $url }}" class="filterLinkSelected" {!! $styleTmp !!}>
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
            <a href="https://twitter.com/wallpapers_name" class="socialBox_social_box_item" aria-label="twitter">
                <i class="fa-brands fa-twitter"></i>
            </a>
        </div>
    </div>
    <div class="socialBox_dmca">
        <a href="https://www.dmca.com/Protection/Status.aspx?ID={{ env('DMCA_ID') }}" 
           title="DMCA.com Protection Status" class="dmca-badge"> 
            <img src="https://images.dmca.com/Badges/dmca-badge-w100-2x1-02.png?ID={{ env('DMCA_ID') }}" alt="DMCA.com Protection Status" loading="lazy" />
        </a>
        <script async src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"></script>
    </div>
</div>

<div class="closeButtonMobileMenu show-1023" onClick="toggleMenuMobile('js_toggleMenuMobile');">
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
                    $(this).closest('ul').css({
                        height : 'auto',
                        opacity : 1
                    });
                    /* mở luôn thẻ chứa các phần tử con của trang hiện tại */ 
                    $(this).next('ul').css({
                        height : 'auto',
                        opacity : 1
                    })
                    $(this).closest('ul').children().each(function(){
                        $(this).removeClass('selected');
                    })
                    
                    $(this).closest('li').addClass('selected');
                    /* thay icon */
                    $(this).closest('ul').closest('li').find('.fa-plus').removeClass('fa-plus').addClass('fa-minus');
                }
            });
        });

        function showHideListMenuMobile(element, idMenu){
            let elementMenu     = $('#'+idMenu);
            let flag            = elementMenu.height();
            if(flag<=0){
                elementMenu.css({
                    height: 'auto',
                    opacity: '1'
                });
            }else {
                elementMenu.css({
                    height: '0',
                    opacity: '0'
                });
            }
            /* toggle icon */
            const elementIcon = $(element).find('i');
            if ($(elementIcon).hasClass('fa-plus')) {
                $(elementIcon).removeClass('fa-plus').addClass('fa-minus');
            } else {
                $(elementIcon).removeClass('fa-minus').addClass('fa-plus');
            }
        }

    </script>
@endpush