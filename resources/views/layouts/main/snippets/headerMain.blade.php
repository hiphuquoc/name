@php
    $categories     = \App\Models\Category::getTreeCategory();
    $brands         = \App\Models\Brand::select('*')
                        ->with('seo')
                        ->get();
@endphp
<div class="headerMain">
    <div class="container">
        <div class="headerMain_item menuTop">
            <div class="headerMain_item_logo">
                <a href="/" class="logoMain" aria-label="Trang chủ {{ config('main.company_name') }}">
                    @if(URL::current()==env('APP_URL'))
                        <h1 style="font-size:0.1rem;">Trang chủ {{ config('main.company_name') }}</h1>
                    @endif
                </a>
            </div>
            <div class="headerMain_item_search">
                @include('main.template.search')
            </div>
            <!-- để chung thể div để thiết lập last-child -->
            <div> 
                <!-- desktop -->
                <div class="hide-990">
                <div class="headerMain_item_text">
                
                    <div class="headerMain_item_text_item maxLine_1">
                        <i class="fa-solid fa-location-dot"></i>{{ config('main.address') }}
                    </div>
                    <div class="headerMain_item_text_item">
                        <div class="hotlineBox">
                            <i class="fa-solid fa-phone"></i>
                            <div class="hotlineBox_hotline">{{ config('main.hotline') }}</div>
                        </div>
                    </div>
                </div>
                </div>
                <!-- mobile -->
                <div class="show-990">
                    <div class="headerMain_item_text">
                        @include('main.snippets.navMenu', compact('categories', 'brands'))
                    </div>
                </div>
            </div>
        </div>
        <div class="headerMain_item menuList">
            <div class="headerMain_item_menu">
                <ul>
                    <li>
                        <div class="hasChild">Nhãn hàng</div>
                            <div class="megaMenu">
                                <div class="megaMenu_content">
                                @if(!empty($brands)&&$brands->isNotEmpty())
                                    <div class="brandGridBox">
                                    @foreach($brands as $brand)
                                        @php
                                            $title  = $brand->name ?? $brand->seo->title ?? null;
                                        @endphp
                                        <a href="/{{ $brand->seo->slug_full ?? null }}" class="brandGridBox_item">
                                            <img src="{{ Storage::url($brand->seo->image_small) }}" alt="{{ $title }}" title="{{ $title }}" />
                                        </a>
                                    @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                {{-- @if(!empty($categories)&&$categories->isNotEmpty()) --}}
                    <li>
                        <div class="hasChild">Nghành hàng</div>
                            <!-- in ra menu category chỉ duyệt đến cấp 2 -->
                            <div class="normalMenu">
                            <ul>
                            @foreach($categories as $category)
                                @php
                                    $name = $category->name ?? $category->seo->title ?? null;
                                    $icon = $category->childs->isNotEmpty() ? '<i class="fas fa-angle-right"></i>' : null;
                                @endphp
                                <li>
                                    <a class="max-line_1" href="/{{ $category->seo->slug_full ?? null }}" title="{{ $name }}">{!! $name.$icon !!}</a>
                                    @if($category->childs->isNotEmpty())
                                        <ul class="right">
                                        @foreach($category->childs as $categoryChild)
                                            @php
                                                $name = $categoryChild->name ?? $categoryChild->seo->title ?? null;
                                            @endphp
                                            <li class="max-line_1">
                                                <a href="/{{ $categoryChild->seo->slug_full ?? null }}" title="{{ $name }}">
                                                    <div>{{ $name }}</div>
                                                </a>
                                            </li>
                                        @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                            </ul>
                        </div>
                    </li>
                {{-- @endif --}}
                <li>
                    <a href="/san-pham-khuyen-mai">Sản phẩm khuyến mãi</a>
                </li>
                <li>
                    <a href="/blog-lam-dep">Làm đẹp</a>
                </li>
                </ul>
            </div>
            <div class="headerMain_item_menuRight">
                
                @if($item->seo->slug_full!='gio-hang')
                    <div id="js_viewSortCart_idWrite" class="headerMain_item_menuRight_item">
                        @include('main.cart.cartSort', ['products' => null])
                    </div>
                @endif
                
                <div class="headerMain_item_menuRight_item js_toggleModalRegistrySeller">
                    <img src="{{ Storage::url('images/svg/icon-user.svg') }}" alt="đăng ký phân phối sản phẩm {{ config('main.company_name') }}" title="đăng ký phân phối sản phẩm {{ config('main.company_name') }}" />
                    <div>Đăng ký phân phối</div>
                </div>
            </div>
        </div>
    </div>
</div>