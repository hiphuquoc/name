@php
    $wallpaperMobile            = [];
    $tmp                        = \App\Models\Category::getTreeCategory();
    foreach($tmp as $categoryLv1){
        if($categoryLv1->seo->slug=='hinh-nen-dien-thoai'){
            $wallpaperMobile    = $categoryLv1;
            break;
        }
    }
    // $wallpaperStyle = \App\Models\Brand::select('brand_info.*')
    //                     ->join('seo', 'seo.id', '=', 'brand_info.seo_id')
    //                     ->orderBy('seo.ordering', 'DESC')
    //                     ->with('seo')
    //                     ->get();
    $policies                   = \App\Models\Page::select('*')
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
            @if(empty($language)||$language=='vi')
                <a href="/" title="Trang chủ {{ config('main.company_name') }}" aria-label="Trang chủ Name.com.vn">
                    <img src="{{ Storage::url('images/svg/house-chimney-blank.svg') }}" alt="Trang chủ {{ config('main.company_name') }}" title="Trang chủ {{ config('main.company_name') }}" />
                    <div>Trang chủ</div>
                </a>
            @else
                <a href="/en" title="Home {{ config('main.company_name') }}" aria-label="Page home Name.com.vn">
                    <img src="{{ Storage::url('images/svg/house-chimney-blank.svg') }}" alt="Home {{ config('main.company_name') }}" title="Home {{ config('main.company_name') }}" />
                    <div>Home</div>
                </a>
            @endif
        </li>
        <li>
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
            
        </li>
        @if(!empty($wallpaperMobile))
            <li>
                @php
                    $titlePhoneWallpaper = empty($language)||$language=='vi' ?  'Hình nền điện thoại' : 'Phone wallpaper';
                    $url      = empty($language)||$language=='vi' ? $wallpaperMobile->seo->slug : $wallpaperMobile->en_seo->slug;
                    $classTmp = 'close';
                    $styleTmp = '';
                    $flagOpen = env('APP_URL').'/'.$url==Request::url() ? true : false;
                    if($flagOpen==true){
                        $classTmp = 'open';
                        $styleTmp = 'style="height:auto;opacity:1;"';
                    }
                @endphp
                <div class="hasChild {{ $classTmp }}">
                    <img src="{{ Storage::url('images/svg/picture-1.svg') }}" alt="{{ $titlePhoneWallpaper }}" title="{{ $titlePhoneWallpaper }}" />
                    @if($flagOpen==true)
                        <div>{{ $titlePhoneWallpaper }}</div>
                    @else 
                        <a href="{{ env('APP_URL') }}/{{ $url }}" arira-label="{{ $wallpaperMobile->name }}">{{ $titlePhoneWallpaper }}</a>
                    @endif
                    <i class="fa-solid fa-plus" onclick="showHideListMenuMobile(this, '{{ $url }}')"></i>
                </div>
                <ul id="{{ $url }}" class="filterLinkSelected" {!! $styleTmp !!}>
                    @foreach($wallpaperMobile->childs as $type)
                        @if($type->products->count()>0)
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
            <div class="hasChild close">
                @if(empty($language)||$language=='vi')
                    <img src="{{ Storage::url('images/svg/headphones.svg') }}" alt="Thông tin hỗ trợ {{ config('main.company_name') }}" title="Thông tin hỗ trợ {{ config('main.company_name') }}" />
                    <div>Hỗ trợ</div>
                    <i class="fa-solid fa-plus"  onclick="showHideListMenuMobile(this, 'ho-tro')"></i>
                @else 
                    <img src="{{ Storage::url('images/svg/headphones.svg') }}" alt="Support infomation" title="Support infomation" />
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
        Kết nối với chúng tôi
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