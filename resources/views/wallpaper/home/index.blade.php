@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.social', compact('item'))
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

    @if(!empty($products)&&$products->isNotEmpty())
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
        @include('wallpaper.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
        <!-- END:: Product Schema -->

        {{-- <!-- STRAT:: FAQ Schema -->
        @include('wallpaper.schema.itemlist', ['data' => $products])
        <!-- END:: FAQ Schema --> --}}

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
    @endif

    <!-- STRAT:: FAQ Schema -->
    @include('wallpaper.schema.faq', ['data' => $item->faqs])
    <!-- END:: FAQ Schema -->
<!-- ===== END:: SCHEMA ===== -->
@endpush
@section('content')

        <!-- share social -->
        @include('wallpaper.template.shareSocial')
        <!-- content -->
        <div class="container">
            <div class="breadcrumbMobileBox"><!-- dùng để chống nhảy padding - margin so với các trang có breadcrumb --></div>
            <!-- === START:: Product Box === -->
            @if(!empty($products)&&$products->isNotEmpty())
                <div class="contentBox">
                    <div class="categoryBox">
                        <div class="categoryBox_title">
                            <h2>
                                @if(empty($language)||$language=='vi')
                                    Hình nền điện thoại mới
                                @else
                                    New Wallpapers
                                @endif
                            </h2>
                        </div>
                        <div class="categoryBox_box">
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
                            <!-- Sản phẩm -->
                            @include('wallpaper.template.wallpaperGrid', [
                                'products'      => $products ?? null,
                                'headingTitle'  => 'h2',
                                'viewBy'        => $viewBy,
                                'total'         => $total,
                                'loaded'        => 5,
                                'id'            => 1,
                                'type'          => 'event_info'
                            ])
                        </div>
                    </div>
                </div>
            @endif
            <!-- === END:: Product Box === -->
                
            <!-- === START:: Product Box === -->
            {{-- @if(!empty($promotionProducts)&&$promotionProducts->isNotEmpty()) --}}
                {{-- <div class="contentBox">
                    <!-- load more -->
                    <input type="hidden" id="js_loadMore_total" name="total" value="{{ $totalPromotionProduct ?? 0 }}" />
                    <input type="hidden" id="js_loadMore_loaded" name="loaded" value="{{ $promotionProducts->count() }}" />
                    @php
                        if(!empty($language)&&$language=='en'){
                            $titleCategoryBox = '<a href="/promotion-phone-wallpapers" title="Promotion phone wallpapers">Promotion phone wallpapers<i class="fa-solid fa-angle-right" style="margin-left:15px;font-size:15px;"></i></a>';
                        }else {
                            $titleCategoryBox = '<a href="/hinh-nen-dien-thoai-khuyen-mai" title="hình nền điện thoại khuyến mãi">Hình nền điện thoại khuyến mãi<i class="fa-solid fa-angle-right" style="margin-left:15px;font-size:15px;"></i></a>';
                        }
                    @endphp
                    @include('wallpaper.home.categoryBox', [
                        'title'     => $titleCategoryBox,
                        'products'  => $promotionProducts,
                        'tagBox'    => 'promotion',
                        'language'  => $language
                    ])
                </div> --}}
            {{-- @endif --}}
            <!-- === END:: Product Box === -->
        </div>
@endsection
@push('modal')
    <!-- Message Add to Cart -->
    <div id="js_addToCart_idWrite">
        @include('wallpaper.cart.cartMessage', [
            'title'     => '',
            'option'    => null,
            'quantity'  => 0,
            'price'     => 0,
            'image'     => null,
            'language'  => $language
        ])
    </div>
@endpush
@push('bottom')
    <!-- Header bottom -->
    @include('wallpaper.snippets.headerBottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('wallpaper.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush