@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Product Schema -->
    @php
    $lowPrice   = 0;
    $highPrice  = 0;
    foreach($products as $product){
        foreach($product->prices as $price){
            if($price->price>$highPrice) $highPrice = $price->price;
            if($price->price<$lowPrice||$lowPrice==0) $lowPrice = $price->price;
        }
    }
    @endphp
    @include('wallpaper.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Product Schema -->

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.social', compact('item', 'lowPrice', 'highPrice'))
    <!-- END:: Title - Description - Social -->

    <!-- STRAT:: Organization Schema -->
    @include('wallpaper.schema.organization')
    <!-- END:: Organization Schema -->


    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.article', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.creativeworkseries', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: FAQ Schema -->
    @include('wallpaper.schema.faq', ['data' => $item->faqs])
    <!-- END:: FAQ Schema -->
<!-- ===== END:: SCHEMA ===== -->
@endpush
@section('content')
    <div style="overflow:hidden;">
        <div class="container">
            <div class="breadcrumbMobileBox">
                @include('wallpaper.template.breadcrumb')
            </div>
            <!-- share social -->
            @include('wallpaper.template.shareSocial')
            <!-- content -->
            <div class="contentBox">
                <h1 style="display:flex;">
                    <div>{{ $titlePage }}</div>
                    <!-- từ khóa vừa search -->
                    @if(!empty(request('key_search')))
                        <div class="keySearchBadge">
                            <div class="keySearchBadge_item">
                                <a href="{{ route('main.searchProduct') }}" class="keySearchBadge_item_badge">
                                    <div>{{ request('key_search') }}</div>
                                    <div class="keySearchBadge_item_badge_action"><i class="fa-solid fa-xmark"></i></div>
                                </a>
                            </div>
                        </div>
                    @endif
                </h1>
                <!-- load more -->
                <input type="hidden" id="js_loadMore_total" name="total" value="{{ $totalProduct ?? 0 }}" />
                <input type="hidden" id="js_loadMore_loaded" name="loaded" value="{{ $products->count() }}" /> 
                {{-- <input type="hidden" id="js_loadMore_keyCategory" name="key_category" value="{{ $keyCategory ?? null }}" />  --}}
                <!-- Sort Box -->
                <div class="sortBox">
                    <div class="sortBox_left">
                        <div><span id="js_filterProduct_count" class="highLight">{{ $totalProduct }}</span> sản phẩm</div>
                    </div>
                    <div class="sortBox_right">
                        <div class="sortBox_right_item">
                            <div style="min-width:100px;">Sắp xếp theo:</div>
                            <select style="max-width:100px;">
                                <option>Mặc định</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- filter box -->
                {{-- <div class="filterStyleWallpaper">
                    @for($i=0;$i<20;++$i)
                        <div class="filterStyleWallpaper_item badge badgePrimary">
                            Phong cách dân dã
                        </div>
                    @endfor
                </div> --}}

                <!-- Product Box -->
                @include('wallpaper.template.wallpaperGrid', [
                    'product'       => $product ?? null,
                    'headingTitle'  => 'h2'
                ])
                {{-- @include('main.template.productGridLoading')
                <div id="js_filterProduct_hidden"></div> --}}
            </div>
            <!-- Nội dung -->
            @if(!empty($content))
                <div id="js_buildTocContentMain_element" class="contentElement contentBox maxContent-1200">
                    <div id="tocContentMain"></div>
                    {!! $content !!}
                </div>
            @endif
            {{-- <div class="categoryWithFilterBox_filter">
                @include('main.category.sidebarContent')
            </div> --}}
            
        </div>
    </div>
@endsection
@push('modal')

@endpush
@push('bottom')
    <!-- Header bottom -->
    @include('wallpaper.snippets.headerBottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        $(window).ready(function(){
            /* load more lần đầu nếu nằm trong vùng xem */
            loadWallpaperSearchMore();

            /* build tocContent khi scroll gần tới */
            const elementBuildTocContent = $('#js_buildTocContentMain_element');
            $(window).on('scroll', function() {
                /* build toc content */
                if(elementBuildTocContent.length){
                    if (!elementBuildTocContent.hasClass('loaded')) {
                        var distance = $(window).scrollTop() - elementBuildTocContent.offset().top + 900;
                        if (distance > 0) {
                            buildTocContentMain('js_buildTocContentMain_element');
                        }
                    }
                }
                /* load more */
                loadWallpaperSearchMore(); 
            });            
        })
    </script>
@endpush