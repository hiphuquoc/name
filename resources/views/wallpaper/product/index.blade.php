@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Product Schema -->
    @php
    $lowPrice   = 0;
    $highPrice  = 0;
    foreach($item->prices as $price){
        if($price->price>$highPrice) $highPrice = $price->price;
        if($price->price<$lowPrice||$lowPrice==0) $lowPrice = $price->price;
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
    <div class="container">        
        <div class="breadcrumbMobileBox">
            @include('wallpaper.template.breadcrumb')
        </div>
        <!-- share social -->
        @include('wallpaper.template.shareSocial')
        <!-- content -->
        <div class="contentBox maxContent-1200">
            <!-- Gallery và Product detail -->
            @include('wallpaper.product.body')

            <!-- Related -->
            @if($totalProduct>0)
            <div class="contentBox">
                <div class="relatedProductBox">
                    <div class="relatedProductBox_title">
                        @if(!empty($language)&&$language=='en')
                            <h2>Recommendations for you</h2>
                        @else 
                            <h2>Gợi ý cho bạn</h2>
                        @endif
                        <!-- load more -->
                        <input type="hidden" id="js_loadMore_total" name="total" value="{{ $totalProduct ?? 0 }}" />
                        <input type="hidden" id="js_loadMore_loaded" name="loaded" value="{{ $related->count() }}" /> 
                        <input type="hidden" id="js_loadMore_keyCategory" name="key_category" value="{{ $keyCategory ?? null }}" /> 
                    </div>
                    <div class="relatedProductBox_box">
                        @include('wallpaper.template.wallpaperGrid', [
                            'products'  => $related
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

    @include('wallpaper.modal.viewImageFull')

    @include('wallpaper.modal.paymentMethod')
@endpush
@push('bottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        $(window).ready(function(){
            // setOptionProduct();

            /* load more */
            loadWallpaperMore();
            $(window).on('scroll', function() {
                loadWallpaperMore();
            });        
        })
        /* thay đổi option sản phẩm */
        function setOptionProduct(idPrice = ''){
            if(idPrice==''){
                var regex   = /product_price_id=(\d+)/;
                var match   = regex.exec(window.location.search);
                idPrice = match[1];
            }
            /* ========= xử lý của button && phần hiển thị giá */
            $(document).find('[data-product_price_id='+idPrice+']').each(function(){
                /* xóa hết selected của button */
                $(this).parent().children().each(function(){
                    $(this).removeClass('selected');
                })
                /* bật lại element được chọn cho button */
                $(this).addClass('selected');
            });
        }
    </script>
@endpush