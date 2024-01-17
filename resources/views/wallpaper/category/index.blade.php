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
    <div class="container">
        <div class="breadcrumbMobileBox">
            @include('wallpaper.template.breadcrumb')
        </div>
        <!-- share social -->
        @include('wallpaper.template.shareSocial')
        <!-- content -->
        <div class="contentBox">
            <div style="display:flex;">
                @php
                    if(empty($language)||$language=='vi'){
                        $titlePage = $item->seo->slug=='hinh-nen-dien-thoai' ? $item->name : 'Hình nền điện thoại '.$item->name;
                    }else {
                        $titlePage = $item->en_seo->slug=='phone-wallpapers' ? $item->en_name : $item->en_name.' Phone Wallpapers';
                    }
                @endphp
                <h1>{{ $titlePage }}</h1>
                <!-- từ khóa vừa search -->
                @if(!empty(request('search')))
                    <div class="keySearchBadge">
                        <div class="keySearchBadge_label">
                            - tìm kiếm với:
                        </div>
                        <div class="keySearchBadge_box">
                            <div class="keySearchBadge_box_item">
                                <div class="keySearchBadge_box_item_badge">
                                    <div>{{ request('search') }}</div>
                                    <a href="{{ URL::current() }}" class="keySearchBadge_box_item_badge_action"><i class="fa-solid fa-xmark"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!-- Sort Box -->
            @php
                $totalSet   = $products->count();
                $totalWallpaper  = 0;
                foreach($products as $product){
                    foreach($product->prices as $price){
                        foreach($price->wallpapers as $wallpaper){
                            ++$totalWallpaper;
                        }
                    }
                }
            @endphp
            @include('wallpaper.template.sort', [
                'language'          => $language ?? 'vi',
                'totalSet'          => $totalSet,
                'totalWallpaper'    => $totalWallpaper,
                'viewBy'            => $viewBy
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
            @php
                $loaded         = 5;
                $arrayIdProduct = [];
                $i              = 0;
                foreach($products as $p) {
                    if($i>=5) $arrayIdProduct[] = $p->id;
                    ++$i;
                }
            @endphp
            @include('wallpaper.template.wallpaperGrid', [
                'products'          => $products ?? null,
                'headingTitle'      => 'h2',
                'contentEmpty'      => true,
                'loaded'            => $loaded,
                'arrayIdProduct'    => $arrayIdProduct
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
@endsection
@push('modal')
    <!-- Message Add to Cart -->
    <div id="js_addToCart_idWrite">
        @include('wallpaper.cart.cartMessage', [
            'title'     => null,
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
            /* build toc content */
            if(elementBuildTocContent.length){
                if (!elementBuildTocContent.hasClass('loaded')) {
                    buildTocContentMain('js_buildTocContentMain_element');
                }
            }    
        })
    </script>
@endpush