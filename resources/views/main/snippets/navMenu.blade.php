<div class="navMobile">
    <div class="navMobile_icon" onclick="openCloseElemt('navMobile')">
        <i class="fa-solid fa-bars"></i>
    </div>
    <div id="navMobile" class="navMobile_menu">
        <div class="navMobile_menu">
            <div class="navMobile_menu_bg" onclick="openCloseElemt('navMobile')"></div>
            <div class="navMobile_menu_main customScrollBar-y">
                <div class="navMobile_menu_main_exit" onclick="openCloseElemt('navMobile')">
                    <i class="fas fa-times"></i>
                </div>
                <a href="/" class="navMobile_menu_main_logo" title="Trang chủ {{ config('main.company_name') }}">
                    <div class="logoMain"></div>
                </a>
                <ul>
                    <li>
                        <a href="/" title="Trang chủ {{ config('main.company_name') }}">
                            <i class="fas fa-home"></i>
                            <div>Trang chủ</div>
                        </a>
                    </li>
                    <li>
                        <div class="hasChild close" onclick="showHideListMenuMobile(this, 'nhanhang')">
                            <i class="fa-solid fa-star"></i>
                            <div>Nhãn hàng</div>
                        </div>
                        <ul id="nhanhang" style="display:none;">
                            @if(!empty($brands)&&$brands->isNotEmpty())
                                @foreach($brands as $brand)
                                    @php
                                        $title  = $brand->name ?? $brand->seo->title ?? null;
                                    @endphp
                                    <li>
                                        <a href="/{{ $brand->seo->slug_full ?? null }}" title="{{ $title }}">
                                            <div>{{ $title }}</div>
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </li>
                    <li>
                        <div class="hasChild close" onclick="showHideListMenuMobile(this, 'nganhhang')">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <div>Nghành hàng</div>
                        </div>
                        <ul id="nganhhang" style="display:none;">
                            @if(!empty($categories)&&$categories->isNotEmpty())
                                @foreach($categories as $category)
                                    @php
                                        $title  = $category->name ?? $category->seo->title ?? null;
                                    @endphp
                                    <li>
                                        <a href="/{{ $category->seo->slug_full ?? null }}" title="{{ $title }}">
                                            <div>{{ $title }}</div>
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </li>
                    <li>
                        <a href="/san-pham-khuyen-mai" title="Sản phẩm đang khuyến mãi trên {{ config('main.company_name') }}">
                            <i class="fa-solid fa-bolt"></i>
                            <div>Sản phẩm khuyến mãi</div>
                        </a>
                    </li>
                    <li>
                        <a href="/blog-lam-dep" title="Blog làm đẹp">
                            <i class="fa-regular fa-bookmark"></i>
                            <div>Làm đẹp</div>
                        </a>
                    </li>
                    <li>
                        <a href="/lien-he" title="Liên hệ {{ config('main.company_name') }}">
                            <i class="fa-solid fa-phone"></i>
                            <div>Liên hệ</div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>