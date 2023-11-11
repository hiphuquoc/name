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
                @if(!empty($language)&&$language=='en')
                    <h1>Promotion phone wallpapers</h1>
                @else
                    <h1>Hình nền điện thoại đang khuyến mãi</h1>
                @endif
                {{-- <!-- load more -->
                <input type="hidden" id="js_loadMore_total" name="total" value="{{ $totalProduct ?? 0 }}" />
                <input type="hidden" id="js_loadMore_loaded" name="loaded" value="{{ $products->count() }}" />  --}}
                <!-- Sort Box -->
                @php
                    $total = $products->count();
                    if($viewBy=='wallpaper'){
                        $total = 0;
                        foreach($products as $product){
                            foreach($product->prices as $price){
                                foreach($price->wallpapers as $wallpaper){
                                    ++$total;
                                }
                            }
                        }
                    }
                @endphp
                @include('wallpaper.template.sort', [
                    'language'  => $language ?? 'vi',
                    'total'     => $total,
                    'viewBy'    => $viewBy
                ])

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
                    'products'      => $products ?? null,
                    'headingTitle'  => 'h2',
                    'language'      => $language
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
            loadWallpaperPromotionMore();

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
                loadWallpaperPromotionMore(); 
            });            
        })
    </script>
@endpush