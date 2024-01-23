@php
    /* chủ đề */
    $wallpaperMobile            = [];
    $tmp                        = \App\Models\Category::getTreeCategory([
        'flag_show' => 1
    ]);
    foreach($tmp as $categoryLv1){
        if($categoryLv1->seo->slug=='hinh-nen-dien-thoai'){
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
    @if(empty($language)||$language=='vi')
        <a href="/" class="logoMain" aria-label="Trang chủ Name.com.vn"></a>
    @else
        <a href="/en" class="logoMain" aria-label="Page home Name.com.vn"></a>
    @endif
</div>
<div class="headerSide customScrollBar-y">
    <ul>
        <li>
            @php
                $icon = file_get_contents('storage/images/svg/icon-home-1.svg');
            @endphp
            @if(empty($language)||$language=='vi')
                <a href="/" title="Trang chủ {{ config('main.company_name') }}" aria-label="Trang chủ Name.com.vn">
                    {!! $icon !!}
                    <div>Trang chủ</div>
                </a>
            @else
                <a href="/en" title="Home {{ config('main.company_name') }}" aria-label="Page home Name.com.vn">
                    {!! $icon !!}
                    <div>Home</div>
                </a>
            @endif
        </li>
        <li>
            @php
                $icon = file_get_contents('storage/images/svg/icon-about-me-2.svg');
            @endphp
            @if(empty($language)||$language=='vi')
                <a href="/ve-chung-toi" title="Về chúng tôi" aria-label="Về chúng tôi">
                    {!! $icon !!}
                    <div>Về chúng tôi</div>
                </a>
            @else
                <a href="/about-us" title="About us" aria-label="About us">
                    {!! $icon !!}
                    <div>About us</div>
                </a>
            @endif
        </li>
        @if(!empty($wallpaperMobile))
            <li>
                @php
                    $titlePhoneWallpaper = empty($language)||$language=='vi' ?  'Chủ Đề hình nền' : 'Wallpaper Themes';
                    $url      = empty($language)||$language=='vi' ? $wallpaperMobile->seo->slug : $wallpaperMobile->en_seo->slug;
                    $classTmp = 'close';
                    $styleTmp = '';
                    $flagOpen = env('APP_URL').'/'.$url==Request::url() ? true : false;
                    if($flagOpen==true){
                        $classTmp = 'open';
                        $styleTmp = 'style="height:auto;opacity:1;"';
                    }
                    $icon = file_get_contents('storage/images/svg/icon-category-2.svg');
                @endphp
                <div class="{{ $classTmp }}">
                    {!! $icon !!}
                    @if($flagOpen==true)
                        <div>{{ $titlePhoneWallpaper }}</div>
                    @else 
                        <a href="{{ env('APP_URL') }}/{{ $url }}" arira-label="{{ $wallpaperMobile->name }}">{{ $titlePhoneWallpaper }}</a>
                    @endif
                    <i class="fa-solid fa-plus" onclick="showHideListMenuMobile(this, '{{ $url }}')"></i>
                </div>
                <ul id="{{ $url }}" class="filterLinkSelected" {!! $styleTmp !!}>
                    @foreach($wallpaperMobile->childs as $type)
                        @if(!empty($type->seo->type)&&$type->seo->type=='category_info'&&$type->products->count()>0)
                            @php
                                if(empty($language)||$language=='vi'){
                                    $title  = $type->name ?? $type->seo->title ?? null;
                                    $url    = $type->seo->slug_full ?? null;
                                }else {
                                    $title  = $type->en_name ?? $type->en_seo->title ?? null;
                                    $url    = $type->en_seo->slug_full ?? null;
                                }
                            @endphp
                            <li>
                                <a href="{{ env('APP_URL') }}/{{ $url }}" title="{{ $title }}" aria-label="{{ $title }}">
                                    <div>{{ $title }} {!! $type->products->count()>0 ? '(<span class="highLight">'.$type->products->count().'</span>)' : null !!}</div>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        @endif
        <li>
            @php
                $icon = file_get_contents('storage/images/svg/icon-brush-1.svg');
            @endphp
            @if(empty($language)||$language=='vi')
                <div class="open">
                    {!! $icon !!}
                    <div style="margin-left:-3px;">Phong Cách hình nền</div>
                    <i class="fa-solid fa-plus"  onclick="showHideListMenuMobile(this, 'phong-cach')"></i>
                </div>
            @else
                <div class="open">
                    {!! $icon !!}
                    <div style="margin-left:-3px;">Wallpaper Styles</div>
                    <i class="fa-solid fa-plus"  onclick="showHideListMenuMobile(this, 'phong-cach')"></i>
                </div>
            @endif
            <ul id="phong-cach" class="filterLinkSelected">
                @foreach($wallpaperMobile->childs as $style)
                    @if(!empty($style->seo->type)&&$style->seo->type=='style_info')
                        @php
                            if(empty($language)||$language=='vi'){
                                $title      = $style->name ?? $style->seo->title ?? null;
                                $urlFull    = env('APP_URL').'/'.$style->seo->slug_full;
                            }else {
                                $title      = $style->en_name ?? $style->en_seo->title ?? null;
                                $urlFull    = env('APP_URL').'/'.$style->en_seo->slug_full;
                            }
                        @endphp
                        <li>
                            <a href="{{  $urlFull }}" title="{{ $title }}" aria-label="{{ $title }}">
                                <div>{{ $title }} {!! $style->products->count()>0 ? '(<span class="highLight">'.$style->products->count().'</span>)' : null !!}</div>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>
        <li>
            @if(empty($language)||$language=='vi')
                <div class="open">
                    <img src="{{ Storage::url('images/svg/icon-event-1.png') }}" alt="Hình nền điện thoại theo sự kiện" title="Hình nền điện thoại theo sự kiện" />
                    <div>Sự Kiện</div>
                    <i class="fa-solid fa-minus"  onclick="showHideListMenuMobile(this, 'su-kien')"></i>
                </div>
            @else
                <div class="open">
                    <img src="{{ Storage::url('images/svg/icon-event-1.png') }}" alt="Phone wallpaper by event" title="Phone wallpaper by event" />
                    <div>Event</div>
                    <i class="fa-solid fa-minus"  onclick="showHideListMenuMobile(this, 'su-kien')"></i>
                </div>
            @endif
            <ul id="su-kien" class="filterLinkSelected" style="height:auto;opacity:1;">
                @foreach($wallpaperMobile->childs as $event)
                    @if(!empty($event->seo->type)&&$event->seo->type=='event_info')
                        @php
                            if(empty($language)||$language=='vi'){
                                $title      = $event->name ?? $event->seo->title ?? null;
                                $urlFull    = env('APP_URL').'/'.$event->seo->slug_full;
                            }else {
                                $title      = $event->en_name ?? $event->en_seo->title ?? null;
                                $urlFull    = env('APP_URL').'/'.$event->en_seo->slug_full;
                            }
                        @endphp
                        <li>
                            <a href="{{  $urlFull }}" title="{{ $title }}" aria-label="{{ $title }}">
                                <div>{{ $title }} {!! $event->products->count()>0 ? '(<span class="highLight">'.$event->products->count().'</span>)' : null !!}</div>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>
        <li>
            @php
                $icon = file_get_contents('storage/images/svg/icon-share-1.svg');
            @endphp
            @if(empty($language)||$language=='vi')
                <a href="{{ env('APP_URL') }}/hinh-nen-dien-thoai/hinh-nen-dien-thoai-mien-phi" title="Hình nền điện thoại miễn phí" aria-label="Hình nền điện thoại miễn phí">
                    {!! $icon !!}
                    <div>Hình nền miễn phí</div>
                </a>
            @else
                <a href="{{ env('APP_URL') }}/phone-wallpapers/free-phone-wallpapers" title="Free phone wallpapers" aria-label="Free phone wallpapers">
                    {!! $icon !!}
                    <div>Free wallpapers</div>
                </a>
            @endif
            
        </li>
        {{-- <li>
            @if(empty($language)||$language=='vi')
                <a href="{{ route('main.saleOff') }}" title="Hình nền điện thoại khuyến mãi" aria-label="Hình nền điện thoại khuyến mãi">
                    <img src="{{ Storage::url('images/svg/percentage.svg') }}" alt="Hình nền điện thoại đang khuyến mãi" title="Hình nền điện thoại đang khuyến mãi" />
                    <div>Đang khuyến mãi</div>
                </a>
            @else
                <a href="{{ route('main.enSaleOff') }}" title="Sale off phone wallpaper" aria-label="Sale off phone wallpaper">
                    <img src="{{ Storage::url('images/svg/percentage.svg') }}" alt="Sale off phone wallpaper" title="Sale off phone wallpaper" />
                    <div>Sale off</div>
                </a>
            @endif
            
        </li> --}}
        <li>
            <div class="close">
                @php
                    $icon = file_get_contents('storage/images/svg/icon-support-1.svg');
                @endphp
                @if(empty($language)||$language=='vi')
                    {!! $icon !!}
                    <div>Hỗ trợ</div>
                    <i class="fa-solid fa-plus"  onclick="showHideListMenuMobile(this, 'ho-tro')"></i>
                @else 
                    {!! $icon !!}
                    <div>Support</div>
                    <i class="fa-solid fa-plus"  onclick="showHideListMenuMobile(this, 'ho-tro')"></i>
                @endif
            </div>
            <ul id="ho-tro" class="filterLinkSelected">
                @foreach($policies as $policy)
                    @php
                        if(empty($language)||$language=='vi'){
                            $title      = $policy->name ?? $policy->seo->title ?? null;
                            $urlFull    = env('APP_URL').'/'.$policy->seo->slug_full;
                        }else {
                            $title      = $policy->en_name ?? $policy->en_seo->title ?? null;
                            $urlFull    = env('APP_URL').'/'.$policy->en_seo->slug_full;
                        }
                    @endphp
                    <li>
                        <a href="{{  $urlFull }}" title="{{ $title }}" aria-label="{{ $title }}">
                            <div>{{ $title }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    </ul>
</div>

<div class="socialBox">
    <div class="socialBox_title">
        @if($language=='vi')
            Kết nối với chúng tôi
        @else 
            Connect with us
        @endif
    </div>
    <div class="socialBox_box">
        <a href="https://www.facebook.com/wallpapers.name.com.vn" class="socialBox_box_item" target="_blank">
            <i class="fa-brands fa-facebook-f"></i>
        </a>
        <a href="https://www.instagram.com/wallpapers_namecomvn" class="socialBox_box_item" target="_blank">
            <i class="fa-brands fa-instagram"></i>
        </a>
        <a href="https://www.youtube.com/@wallpapers_namecomvn" class="socialBox_box_item" target="_blank">
            <i class="fa-brands fa-youtube"></i>
        </a>
        <a href="https://www.tiktok.com/@wallpapers_namecomvn" class="socialBox_box_item" target="_blank">
            <i class="fa-brands fa-tiktok"></i>
        </a>
        <a href="https://twitter.com/wallpapers_name" class="socialBox_box_item" target="_blank">
            <i class="fa-brands fa-twitter"></i>
        </a>
    </div>
</div>

<div class="closeButtonMobileMenu show-1023" onClick="toggleMenuMobile('js_toggleMenuMobile');">
    <i class="fa-sharp fa-solid fa-xmark"></i>
</div>
<div class="backgroundBlurMobileMenu" onClick="toggleMenuMobile('js_toggleMenuMobile');"></div>

@push('scriptCustom')
    <script type="text/javascript">

        $(window).ready(function(){
            var Url             = document.URL;
            // var elementMenu     = null;
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
        })

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
            if ($(element).hasClass('fa-plus')) {
                $(element).removeClass('fa-plus').addClass('fa-minus');
            } else {
                $(element).removeClass('fa-minus').addClass('fa-plus');
            }
        }

    </script>
@endpush