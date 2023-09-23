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
                <a href="/" title="Trang chủ {{ config('main.company_name') }}">
                    <img src="{{ Storage::url('images/svg/house-chimney-blank.svg') }}" alt="Trang chủ {{ config('main.company_name') }}" title="Trang chủ {{ config('main.company_name') }}" />
                    <div>Trang chủ</div>
                </a>
            @else
                <a href="/en" title="Home {{ config('main.company_name') }}">
                    <img src="{{ Storage::url('images/svg/house-chimney-blank.svg') }}" alt="Home {{ config('main.company_name') }}" title="Home {{ config('main.company_name') }}" />
                    <div>Home</div>
                </a>
            @endif
        </li>
        <li>
            @if(empty($language)||$language=='vi')
                <a href="{{ route('main.saleOff') }}" title="Hình nền điện thoại khuyến mãi">
                    <img src="{{ Storage::url('images/svg/percentage.svg') }}" alt="Hình nền điện thoại đang khuyến mãi" title="Hình nền điện thoại đang khuyến mãi" />
                    <div>Đang khuyến mãi</div>
                </a>
            @else
                <a href="{{ route('main.saleOff') }}" title="Sale off phone wallpaper">
                    <img src="{{ Storage::url('images/svg/percentage.svg') }}" alt="Sale off phone wallpaper" title="Sale off phone wallpaper" />
                    <div>Sale off</div>
                </a>
            @endif
            
        </li>
        @if(!empty($wallpaperMobile))
            <li>
                <div class="hasChild open" onclick="showHideListMenuMobile(this, '{{ $wallpaperMobile->seo->slug }}')">
                    @php
                        $titlePhoneWallpaper = empty($language)||$language=='vi' ?  'Hình nền điện thoại' : 'Phone wallpaper';
                    @endphp
                    <img src="{{ Storage::url('images/svg/picture-1.svg') }}" alt="{{ $titlePhoneWallpaper }}" title="{{ $titlePhoneWallpaper }}" />
                    <div>{{ $titlePhoneWallpaper }}</div>
                </div>
                <ul id="{{ $wallpaperMobile->seo->slug }}" style="height:auto;opacity:1;">
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
                                <a href="{{ env('APP_URL') }}/{{ $url }}" title="{{ $title }}">
                                <div>{{ $title }} {!! $type->products->count()>0 ? '(<span class="highLight">'.$type->products->count().'</span>)' : null !!}</div>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        @endif
        {{-- <li>
            <div class="hasChild close" onclick="showHideListMenuMobile(this, 'phong-cach')">
                <img src="{{ Storage::url('images/svg/bookmark.svg') }}" />
                <div>Theo phong cách</div>
            </div>
            <ul id="phong-cach">
                @foreach($wallpaperStyle as $style)
                    @php
                        $title      = $style->name ?? $style->seo->title ?? null;
                        $selected   = $loop->index==3 ? 'selected' : null;
                    @endphp
                    <li class="{{ $selected }}">
                        <a href="/{{ $style->seo->slug_full ?? null }}" title="{{ $title }}">
                        <div>{{ $title }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li> --}}

        {{-- <li>
            <a href="/san-pham-khuyen-mai" title="Sản phẩm đang khuyến mãi trên Hoaanhtuc">
                <img src="{{ Storage::url('images/svg/comment.svg') }}" />
                <div>Tản mạn</div>
            </a>
        </li> --}}
        
        <li>
            <div class="hasChild close" onclick="showHideListMenuMobile(this, 'ho-tro')">
                @if(empty($language)||$language=='vi')
                    <img src="{{ Storage::url('images/svg/headphones.svg') }}" alt="Support infomation" title="Support infomation" />
                    <div>Support</div>
                @else 
                    <img src="{{ Storage::url('images/svg/headphones.svg') }}" alt="Thông tin hỗ trợ {{ config('main.company_name') }}" title="Thông tin hỗ trợ {{ config('main.company_name') }}" />
                    <div>Hỗ trợ</div>
                @endif
            </div>
            <ul id="ho-tro">
                @foreach($policies as $policy)
                    @php
                        if(empty($language)||$language=='vi'){
                            $title      = $policy->en_name ?? $policy->en_seo->title ?? null;
                            $urlFull    = env('APP_URL').'/'.$policy->en_seo->slug_full;
                        }else {
                            $title      = $policy->name ?? $policy->seo->title ?? null;
                            $urlFull    = env('APP_URL').'/'.$policy->seo->slug_full;
                        }
                    @endphp
                    <li>
                        <a href="{{  $urlFull }}" title="{{ $title }}">
                            <div>{{ $title }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    </ul>
</div>
<div class="closeButtonMobileMenu show-1023" onClick="toggleMenuMobile('js_toggleMenuMobile');">
    <i class="fa-sharp fa-solid fa-xmark"></i>
</div>
<div class="backgroundBlurMobileMenu" onClick="toggleMenuMobile('js_toggleMenuMobile');"></div>