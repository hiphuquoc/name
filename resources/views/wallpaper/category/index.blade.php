@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Product Schema -->
    @php
        $highPrice          = 0;
        $lowPrice           = 0;
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
                <h1>{{ config('language.'.$language.'.data.phone_wallpaper').' '.$itemSeo->title }}</h1>
            </div>
            <!-- Sort Box -->
            @include('wallpaper.category.sort', [
                'language'          => $language ?? 'vi',
                'total'             => $total
            ])
            <!-- Box 
                vừa vào tải 0 phần tử -> tất cả tải bằng ajax
            -->
            @include('wallpaper.category.box', [
                'wallpapers'        => new \Illuminate\Database\Eloquent\Collection,
                'total'             => $total,
                'loaded'            => 0,
                'arrayIdCategory'   => $arrayIdCategory,
                'language'          => $language
            ])
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