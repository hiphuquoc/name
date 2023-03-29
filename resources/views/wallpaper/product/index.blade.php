@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Title - Description - Social -->
    @include('main.schema.social', compact('item'))
    <!-- END:: Title - Description - Social -->

    <!-- STRAT:: Organization Schema -->
    @include('main.schema.organization')
    <!-- END:: Organization Schema -->


    <!-- STRAT:: Article Schema -->
    @include('main.schema.article', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: Article Schema -->
    @include('main.schema.creativeworkseries', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: Product Schema -->
    @php
        $lowPrice   = 0;
        $highPrice  = 0;
        foreach($item->prices as $price){
            if($price->price>$highPrice) $highPrice = $price->price;
            if($price->price<$lowPrice||$lowPrice==0) $lowPrice = $price->price;
        }
    @endphp
    @include('main.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Product Schema -->

    <!-- STRAT:: FAQ Schema -->
    @include('main.schema.faq', ['data' => $item->faqs])
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
            @if(!empty($related)&&$related->isNotEmpty())
            <div class="contentBox">
                <div class="relatedProductBox">
                    <div class="relatedProductBox_title">
                        <h2>Gợi ý cho bạn</h2>
                    </div>
                    <div class="relatedProductBox_box">
                        @include('wallpaper.template.wallpaperGrid', [
                            'products'  => $related,
                            'type'      => 'lazyload'
                        ])
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
@push('modal')
    @include('wallpaper.modal.viewImageFull')
@endpush
@push('bottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        // $(window).ready(function(){
        //     setOptionProduct();
        // })
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