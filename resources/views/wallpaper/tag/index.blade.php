@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Product Schema -->
    @php
        if(empty($language)||$language=='vi'){
            $currency           = 'VND';
            $highPrice          = 0;
            foreach($wallpapers as $wallpaper){
                if($wallpaper->price_before_promotion>$highPrice) $highPrice = \App\Helpers\Number::convertUSDToVND($wallpaper->price_before_promotion);
            }
            $lowPrice           = $highPrice;
            foreach($wallpapers as $wallpaper){
                if(!empty($wallpaper->prices)){ /* trường hợp hình nền miễn phí không có giá */
                    foreach($wallpaper->prices as $price){
                        if($price->price<$lowPrice) $lowPrice   = \App\Helpers\Number::convertUSDToVND($price->price);
                    }
                }
            }
        }else {
            $currency           = 'USD';
            $highPrice          = 0;
            foreach($wallpapers as $wallpaper){
                if($wallpaper->price_before_promotion>$highPrice) $highPrice = $wallpaper->price_before_promotion;
            }
            $lowPrice           = $highPrice;
            foreach($wallpapers as $wallpaper){
                if(!empty($wallpaper->prices)){ /* trường hợp hình nền miễn phí không có giá */
                    foreach($wallpaper->prices as $price){
                        if($price->price<$lowPrice) $lowPrice = $price->price;
                    }
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
    @include('wallpaper.schema.itemlist', ['data' => $wallpapers])
    <!-- END:: FAQ Schema -->

    <!-- STRAT:: ImageObject Schema -->
    @php
        $dataImages = new \Illuminate\Database\Eloquent\Collection;
        foreach($wallpapers as $wallpaper){
            if(!empty($wallpaper->prices)){ /* trường hợp hình nền miễn phí không có giá */
                foreach($wallpaper->prices as $price){
                    foreach($price->wallpapers as $wallpaper) {
                        $dataImages[] = $wallpaper->infoWallpaper;
                    }
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
                <h1>Hình nền điện thoại {{ empty($language)||$language=='vi' ? $item->name : $item->en_name }}</h1>
            </div>
            <!-- Sort Box -->
            @include('wallpaper.tag.sort', [
                'language'          => $language ?? 'vi',
                'total'             => $total
            ])
            <!-- Product Box -->
            <!-- load more -->
            <input type="hidden" id="total" name="total" value="{{ $total }}" />
            <input type="hidden" id="loaded" name="loaded" value="{{ $loaded ?? 0 }}" />
            <input type="hidden" id="topLoad" name="topLoad" value="" />
            <input type="hidden" id="typeWhere" name="typeWhere" value="{{ $typeWhere ?? 'or' }}" />
            <input type="hidden" id="arrayIdCategory" name="arrayIdCategory" value="{{ json_encode($arrayIdCategory) }}" />
            <div class="freeWallpaperBox">
                @if($total>0)
                    @foreach($wallpapers as $wallpaper)
                        @include('wallpaper.category.item', [
                            'wallpaper' => $wallpaper,
                            'language'  => $language,
                            'user'      => $user ?? null
                        ])
                    @endforeach
                @else 
                    <div>{{ config('language.'.$language.'.data.no_suitable_results_found') }}</div>
                @endif
            </div>
        </div>
        <!-- Nội dung -->
        @if(!empty($itemSeo->contents))
            <div id="js_buildTocContentMain_element" class="contentElement contentBox maxContent-1200">
                <div id="tocContentMain"></div>
                @php
                    $xhtmlContent = '';
                    foreach($itemSeo->contents as $content) $xhtmlContent .= $content->content;
                @endphp
                {!! $xhtmlContent !!}
            </div>
        @endif
        {{-- <div class="categoryWithFilterBox_filter">
            @include('main.tag.sidebarContent')
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
            /* build tocContent khi scroll gần tới */
            const elementBuildTocContent = $('#js_buildTocContentMain_element');
            /* build toc content */
            if(elementBuildTocContent.length){
                if (!elementBuildTocContent.hasClass('loaded')) {
                    buildTocContentMain('js_buildTocContentMain_element');
                }
            }  
            // /* lazyload image */
            // lazyload();     
        })
    </script>
@endpush