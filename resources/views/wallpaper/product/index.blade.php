@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Product Schema -->
    @php
        if(empty($language)||$language=='vi'){
            $currency           = 'VND';
            $highPrice          = \App\Helpers\Number::convertUSDToVND($item->price_before_promotion);
            $lowPrice           = $highPrice;
            foreach($item->prices as $price){
                if($price->price<$lowPrice){
                    $lowPrice   = \App\Helpers\Number::convertUSDToVND($price->price);
                }
            }
        }else {
            $currency           = 'USD';
            $highPrice          = $item->price_before_promotion;
            $lowPrice           = $highPrice;
            foreach($item->prices as $price){
                if($price->price<$lowPrice){
                    $lowPrice   = $price->price;
                }
            }
    }
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

    <!-- STRAT:: ImageObject Schema -->
    @php
        $dataImages = new \Illuminate\Database\Eloquent\Collection;
        foreach($item->prices as $price){
            foreach($price->wallpapers as $wallpaper) $dataImages[] = $wallpaper->infoWallpaper;
        }
    @endphp
    @include('wallpaper.schema.imageObject', ['data' => $dataImages])
    <!-- END:: ImageObject Schema -->

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

            <!-- Content -->
            @include('wallpaper.product.content', ['contents' => $item->contents])

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
                    </div>
                    <div class="relatedProductBox_box">
                        @php
                            $arrayIdProduct = [];
                            foreach($related as $p) $arrayIdProduct[] = $p->infoProduct->id;
                        @endphp
                        @include('wallpaper.template.wallpaperGrid', [
                            'loaded'            => 0,
                            'contentEmpty'      => true,
                            'arrayIdProduct'    => $arrayIdProduct
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
        })
        /* thay đổi option sản phẩm */
        function setOptionProduct(element, idProduct, type){
            /* xử lý cho việc thay đổi button */
            $(element).parent().children().each(function(){
                $(this).removeClass('selected');
            });
            $(element).addClass('selected');
            /* xử lý cho việc hiển thị lại giá theo option */
            const idKey         = $(element).data('product_price_id');
            const elementPrice  = $('#'+idKey);
            elementPrice.parent().children().each(function(){
                $(this).removeClass('selected');
            });
            elementPrice.addClass('selected');
            /* set lại sự kiện button addToCart cho đúng option vừa chọn */
            $('#js_addToCart_button').attr("onclick", "addToCart('"+idProduct+"', '"+idKey+"', '"+type+"');");
        }
    </script>
@endpush