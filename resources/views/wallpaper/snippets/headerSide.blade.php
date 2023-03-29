@php
    $wallpaperMobile          = [];
    $tmp                    = \App\Models\Category::getTreeCategory();
    foreach($tmp as $categoryLv1){
        if($categoryLv1->seo->slug=='hinh-nen-dien-thoai'){
            $wallpaperMobile  = $categoryLv1;
            break;
        }
    }
    // $wallpaperStyle = \App\Models\Brand::select('brand_info.*')
    //                     ->join('seo', 'seo.id', '=', 'brand_info.seo_id')
    //                     ->orderBy('seo.ordering', 'DESC')
    //                     ->with('seo')
    //                     ->get();
@endphp             
<div class="logoInMenuMobile show-1023">
    <div class="logoMain"></div>
</div>
<div class="headerSide customScrollBar-y">
    <ul>
        <li>
            <a href="/" title="Trang chủ {{ config('main.company_name') }}">
                <img src="{{ Storage::url('images/svg/house-chimney-blank.svg') }}" alt="Trang chủ {{ config('main.company_name') }}" title="Trang chủ {{ config('main.company_name') }}" />
                <div>Trang chủ</div>
            </a>
        </li>
        @if(!empty($wallpaperMobile))
            <li>
                <div class="hasChild open" onclick="showHideListMenuMobile(this, '{{ $wallpaperMobile->seo->slug }}')">
                    <img src="{{ Storage::url('images/svg/picture-1.svg') }}" alt="Hình nền điện thoại" title="Hình nền điện thoại" />
                    <div>Hình nền điện thoại</div>
                </div>
                <ul id="{{ $wallpaperMobile->seo->slug }}" style="height:auto;opacity:1;">
                    @foreach($wallpaperMobile->childs as $type)
                        @if($type->products->count()>0)
                            @php
                                $title      = $type->name ?? $type->seo->title ?? null;
                            @endphp
                            <li>
                                <a href="{{ env('APP_URL') }}/{{ $type->seo->slug_full ?? null }}" title="{{ $title }}">
                                <div>{{ $title }} {!! $type->products->count()>0 ? '(<span class="highLight">'.$type->products->count().'</span>)' : null !!}</div>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        @endif
        <li>
            <a href="{{ route('main.saleOff') }}" title="Hình nền điện thoại khuyến mãi">
                <img src="{{ Storage::url('images/svg/percentage.svg') }}" alt="Hình nền điện thoại đang khuyến mãi" title="Hình nền điện thoại đang khuyến mãi" />
                <div>Đang khuyến mãi</div>
            </a>
        </li>
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
            <a href="/san-pham-khuyen-mai" title="Sản phẩm đang khuyến mãi trên {{ config('main.company_name') }}">
                <img src="{{ Storage::url('images/svg/headphones.svg') }}" alt="Thông tin hỗ trợ {{ config('main.company_name') }}" title="Thông tin hỗ trợ {{ config('main.company_name') }}" />
                <div>Hỗ trợ</div>
            </a>
        </li>
    </ul>
</div>
<div class="closeButtonMobileMenu show-1023" onClick="toggleMenuMobile('js_toggleMenuMobile');">
    <i class="fa-sharp fa-solid fa-xmark"></i>
</div>
<div class="backgroundBlurMobileMenu" onClick="toggleMenuMobile('js_toggleMenuMobile');"></div>