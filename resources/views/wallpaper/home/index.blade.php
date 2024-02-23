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
        @if(!empty($infoCategoryTet))
            <div class="contentBox">
                <div class="categoryBox">
                    <div class="categoryBox_title">
                        <h2>
                            @if(empty($language)||$language=='vi')
                                <a href="/{{ $infoCategoryTet->seo->slug_full ?? null }}" aria-label="Hình nền điện thoại Gái xinh">Hình nền điện thoại Gái xinh</a>
                            @else
                                <a href="/{{ $infoCategoryTet->en_seo->slug_full ?? null }}" aria-label="Pretty girl phone wallpapers">Pretty girl phone wallpapers</a>
                            @endif
                        </h2>
                    </div>
                    <div class="categoryBox_box">
                        <!-- Hình nền điện thoại tết -->
                        @php
                            $productGirl = new \Illuminate\Database\Eloquent\Collection;
                            foreach($infoCategoryGirl->products as $product){
                                /* lọc bỏ các phần tử chưa có wallpaper => chưa lọc được trong query */
                                if(!empty($product->infoProduct->prices)&&$product->infoProduct->prices->isNotEmpty()) {
                                    $flagHaveWallpaper = false;
                                    foreach($product->infoProduct->prices as $price){
                                        if($price->wallpapers->isNotEmpty()){
                                            $flagHaveWallpaper = true;
                                            break;
                                        }
                                    }
                                    if($flagHaveWallpaper==true) $productGirl->add($product->infoProduct);
                                }
                            }
                        @endphp
                        @include('wallpaper.template.wallpaperGrid', [
                            'wallpapers'    => $productGirl ?? null,
                            'headingTitle'  => 'h2',
                            'viewBy'        => $viewBy
                        ])
                    </div>
                </div>
            </div>
        @endif
        <!-- === END:: Product Box === -->

        <!-- === START:: Product Box === -->
        @if(!empty($infoCategoryTet))
            <div class="contentBox">
                <div class="categoryBox">
                    <div class="categoryBox_title">
                        <h2>
                            @if(empty($language)||$language=='vi')
                                <a href="/{{ $infoCategoryTet->seo->slug_full ?? null }}" aria-label="Hình nền điện thoại Tết 2024">Hình nền điện thoại Tết 2024</a>
                            @else
                            <a href="/{{ $infoCategoryTet->en_seo->slug_full ?? null }}" aria-label="New Year 2024 Wallpapers">New Year 2024 Wallpapers</a>
                            @endif
                        </h2>
                    </div>
                    <div class="categoryBox_box">
                        <!-- Hình nền điện thoại tết -->
                        @php
                            $productTet = new \Illuminate\Database\Eloquent\Collection;
                            foreach($infoCategoryTet->products as $product){
                                /* lọc bỏ các phần tử chưa có wallpaper => chưa lọc được trong query */
                                if(!empty($product->infoProduct->prices)&&$product->infoProduct->prices->isNotEmpty()) {
                                    $flagHaveWallpaper = false;
                                    foreach($product->infoProduct->prices as $price){
                                        if($price->wallpapers->isNotEmpty()){
                                            $flagHaveWallpaper = true;
                                            break;
                                        }
                                    }
                                    if($flagHaveWallpaper==true) $productTet->add($product->infoProduct);
                                }
                            }
                        @endphp
                        @include('wallpaper.template.wallpaperGrid', [
                            'wallpapers'    => $productTet ?? null,
                            'headingTitle'  => 'h2',
                            'viewBy'        => $viewBy,
                            'total'         => $productTet->count(),
                            'loaded'        => $productTet->count(),
                            'arrayId'       => [0],
                            'type'          => 'event_info'
                        ])
                    </div>
                </div>
            </div>
        @endif
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