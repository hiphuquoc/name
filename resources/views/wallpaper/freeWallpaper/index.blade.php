@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Product Schema -->
    @php
        $highPrice          = 0;
        $lowPrice           = 0;
        $currency           = empty($language)||$language=='vi' ? 'VND' : 'USD';
    @endphp
    @include('wallpaper.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice, 'currentcy' => $currency])
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

    {{-- <!-- STRAT:: ImageObject Schema -->
    @php
        $dataImages = new \Illuminate\Database\Eloquent\Collection;
        foreach($item->prices as $price){
            foreach($price->wallpapers as $wallpaper) $dataImages[] = $wallpaper->infoWallpaper;
        }
    @endphp
    @include('wallpaper.schema.imageObject', ['data' => $dataImages])
    <!-- END:: ImageObject Schema --> --}}

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.creativeworkseries', compact('item'))
    <!-- END:: Article Schema -->

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
        <div class="contentBox maxContent-1200">
            <!-- Gallery và Product detail -->
            @include('wallpaper.freeWallpaper.body')

            <!-- Related -->
            @if($total>0)
                <div class="contentBox">
                    <div class="relatedProductBox">
                        <div class="relatedProductBox_title">
                            @if(!empty($language)&&$language=='en')
                                <h2>Recommendations for you</h2>
                            @else 
                                <h2>Gợi ý cho bạn</h2>
                            @endif
                        </div>
                        <div class="relatedProductBox_box">
                            @include('wallpaper.category.box', [
                                'total'             => $total,
                                'loaded'            => $loaded,
                                'arrayIdCategoyr'   => $arrayIdCategory,
                                'wallpapers'        => $related,
                                'language'          => $language,
                                'idNot'             => $idNot
                            ])
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('modal')
    <!-- Message Add to Cart -->
    <div id="js_addToCart_idWrite">
        @include('wallpaper.cart.cartMessage', [
            'title'     => $item->name,
            'option'    => null,
            'quantity'  => 0,
            'price'     => 0,
            'image'     => null,
            'language'  => $language
        ])
    </div>

    {{-- @include('wallpaper.modal.viewImageFull')

    @include('wallpaper.modal.paymentMethod') --}}
@endpush
@push('bottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        $(window).ready(function(){
           
        })

        /* thả cảm xúc */
        function toogleHeartFeelingFreeWallpaper(idFreeWallpaper){
            $.ajax({
                url         : '{{ route("ajax.toogleHeartFeelingFreeWallpaper") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    free_wallpaper_info_id : idFreeWallpaper
                },
                success     : function(response){
                    if(response==true){
                        $('.freeWallpaperDetailBox .heart').addClass('selected');
                    }else {
                        $('.freeWallpaperDetailBox .heart').removeClass('selected');
                    }
                }
            });
        }
    </script>
@endpush