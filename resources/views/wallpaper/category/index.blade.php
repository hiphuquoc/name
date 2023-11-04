@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Product Schema -->
    @php
        if(empty($language)||$language=='vi'){
            $currency           = 'VND';
            $highPrice          = 0;
            foreach($products as $product){
                if($product->price_before_promotion>$highPrice) $highPrice = \App\Helpers\Number::convertUSDToVND($product->price_before_promotion);
            }
            $lowPrice           = $highPrice;
            foreach($products as $product){
                foreach($product->prices as $price){
                    if($price->price<$lowPrice) $lowPrice   = \App\Helpers\Number::convertUSDToVND($price->price);
                }
            }
        }else {
            $currency           = 'USD';
            $highPrice          = 0;
            foreach($products as $product){
                if($product->price_before_promotion>$highPrice) $highPrice = $product->price_before_promotion;
            }
            $lowPrice           = $highPrice;
            foreach($products as $product){
                foreach($product->prices as $price){
                    if($price->price<$lowPrice) $lowPrice = $price->price;
                }
            }
        }
    @endphp
    @include('wallpaper.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice, 'currency' => $currency])
    <!-- END:: Product Schema -->

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.social', compact('item', 'lowPrice', 'highPrice'))
    <!-- END:: Title - Description - Social -->

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.breadcrumb', compact('breadcrumb'))
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
    @include('wallpaper.schema.itemlist', ['data' => $products])
    <!-- END:: FAQ Schema -->

    <!-- STRAT:: ImageObject Schema -->
    @php
        $dataImages = new \Illuminate\Database\Eloquent\Collection;
        foreach($products as $product){
            foreach($product->prices as $price){
                foreach($price->wallpapers as $wallpaper) {
                    $dataImages[] = $wallpaper->infoWallpaper;
                }
            }
        }
    @endphp
    @include('wallpaper.schema.imageObject', ['data' => $dataImages])
    <!-- END:: ImageObject Schema -->

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
                    @if(!empty($item->en_name)&&$item->en_name!='Phone Wallpapers')
                        <h1>{{ $item->en_name }} Phone Wallpapers</h1>
                    @else 
                        <h1>{{ $item->en_name ?? null }}</h1>
                    @endif
                @else 
                    @if(!empty($item->name)&&$item->name!='Hình nền điện thoại')
                        <h1>Hình nền điện thoại {{ $item->name }}</h1>
                    @else 
                        <h1>{{ $item->name ?? null }}</h1>
                    @endif
                @endif
                <!-- load more -->
                {{-- <input type="hidden" id="js_loadMore_total" name="total" value="{{ $totalProduct ?? 0 }}" />
                <input type="hidden" id="js_loadMore_loaded" name="loaded" value="{{ $products->count() }}" /> 
                <input type="hidden" id="js_loadMore_keyCategory" name="key_category" value="{{ $keyCategory ?? null }}" />  --}}

                <!-- Sort Box -->
                @include('wallpaper.template.sort', [
                    'language'  => $language ?? 'vi',
                    'total'     => $totalProduct
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
            // /* load more lần đầu nếu nằm trong vùng xem */
            // loadWallpaperMore();

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
                // /* load more */
                // loadWallpaperMore(); 
            });            
        })
    </script>
@endpush