@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Organization Schema -->
    @include('wallpaper.schema.organization')
    <!-- END:: Organization Schema -->

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.article', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.creativeworkseries', compact('item'))
    <!-- END:: Article Schema -->

    {{-- <!-- STRAT:: Product Schema -->
    @php
        $highPrice          = 0;
        if(!empty($products))foreach($products as $product){
            if($product->price_before_promotion>$highPrice) $highPrice = $product->price_before_promotion;
        }
        $lowPrice           = $highPrice;
        foreach($products as $product){
            foreach($product->prices as $price){
                if($price->price<$lowPrice) $lowPrice   = $price->price;
            }
        }
    @endphp
    @include('wallpaper.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Product Schema -->

    <!-- STRAT:: FAQ Schema -->
    @include('wallpaper.schema.itemlist', ['data' => $products])
    <!-- END:: FAQ Schema -->

    <!-- STRAT:: ImageObject Schema -->
    @include('wallpaper.schema.imageObject', ['data' => $products])
    <!-- END:: ImageObject Schema --> --}}

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.social', ['item' => $item, 'lowPrice' => 0, 'highPrice' => 0])
    <!-- END:: Title - Description - Social -->

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
        
        <!-- Item Category Grid Box -->
        @foreach(config('main.category_type') as $type)
            <div class="contentBox">
                <div class="categoryGrid">
                    <div class="categoryGrid_title">
                        @php
                            $titleBox = '';
                            if($type['key']=='category_info') $titleBox = config('language.'.$language.'.data.wallpaper_by_themes');
                            if($type['key']=='style_info') $titleBox = config('language.'.$language.'.data.wallpaper_by_styles');
                            if($type['key']=='event_info') $titleBox = config('language.'.$language.'.data.wallpaper_by_events');
                        @endphp
                        <h2>{{ $titleBox }}</h2>
                    </div>
                    <div class="categoryGrid_box">
                        @foreach($categories as $category)

                            @foreach($category->seos as $categorySeo)
                                @if(!empty($categorySeo->infoSeo->type)&&$categorySeo->infoSeo->type==$type['key'])
                                    @php
                                        $categoryName   = $categorySeo->infoSeo->title ?? null;
                                        $categoryUrl    = env('APP_URL').'/'.$categorySeo->infoSeo->slug_full;
                                        $categoryThumbMini  = config('image.default');
                                        $categoryThumbSmall  = config('image.default');
                                        if(!empty($category->seo->image)) {
                                            $categoryThumbMini = \App\Helpers\Image::getUrlImageMiniByUrlImage($category->seo->image); 
                                            $categoryThumbSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($category->seo->image); 
                                        }             
                                    @endphp
                                    <div class="categoryGrid_box_item">
                                        <a href="{{ $categoryUrl }}" class="categoryGrid_box_item_image">
                                            <img class="lazyload" src="{{ $categoryThumbMini }}" data-src="{{ $categoryThumbSmall }}" alt="{{ $categoryName }}" title="{{ $categoryName }}" />
                                        </a>
                                        <div class="categoryGrid_box_item_content">
                                            <a href="{{ $categoryUrl }}" class="categoryGrid_box_item_content_title">
                                                <h2>{{ $categoryName }}</h2>
                                            </a>
                                            @if(!empty($category->tags)&&$category->tags->isNotEmpty())
                                                <div class="categoryGrid_box_item_content_list">
                                                    @foreach($category->tags as $tag)
                                                        @foreach($tag->infoTag->seos as $tagSeo)
                                                            @if($tagSeo->infoSeo->language==$language)
                                                                <a href="/{{ $tagSeo->infoSeo->slug_full }}">{{ $tagSeo->infoSeo->title }}</a>
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @break
                                @endif
                            @endforeach

                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

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