@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Product Schema -->
    @php
        $highPrice          = 0;
        foreach($wallpapers as $wallpaper){
            if($wallpaper->price_before_promotion>$highPrice) $highPrice = $wallpaper->price_before_promotion;
        }
        $lowPrice           = $highPrice;
        foreach($wallpapers as $wallpaper){
            foreach($wallpaper->prices as $price){
                if($price->price<$lowPrice) $lowPrice   = $price->price;
            }
        }
    @endphp
    @include('wallpaper.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Product Schema -->

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.social', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
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
    @include('wallpaper.schema.imageObject', ['data' => $wallpapers])
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
                    $titlePage = config('language.'.$language.'.data.phone_wallpaper').$itemSeo->title;
                    if($item->seo->level==1) $titlePage = $itemSeo->title;
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
                $totalSet   = $wallpapers->count();
                $totalWallpaper  = 0;
                foreach($wallpapers as $wallpaper){
                    foreach($wallpaper->prices as $price){
                        foreach($price->wallpapers as $wallpaper){
                            ++$totalWallpaper;
                        }
                    }
                }
            @endphp
            @include('wallpaper.categoryMoney.sort', [
                'language'          => $language ?? 'vi',
                'totalSet'          => $totalSet,
                'totalWallpaper'    => $totalWallpaper,
                'viewBy'            => $viewBy
            ])

            <!-- Product Box 
                vừa vào tải 0 phần tử -> tất cả tải bằng ajax
            -->
            @include('wallpaper.template.wallpaperGridWithLoadMore', [
                'wallpapers'        => $wallpapers,
                'headingTitle'      => 'h2',
                'contentEmpty'      => true,
                'loaded'            => 0,
                'total'             => $total,
                'empty'             => !empty($wallpapers)&&$wallpapers->isNotEmpty() ? false : true
            ])
            <!-- Loading -->
            <div class="loadingBox">
                <span class="loadingIcon"></span>
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
        })
    </script>
@endpush