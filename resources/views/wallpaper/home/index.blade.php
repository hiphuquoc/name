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

    <!-- STRAT:: Product Schema -->
    @php
        $lowPrice   = 15000;
        $highPrice  = 45000;
    @endphp
    @include('wallpaper.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Product Schema -->

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
            <!-- === START:: Product Box === -->
            @if(!empty($newProducts)&&$newProducts->isNotEmpty())
                {{-- <div class="contentBox">
                    @include('wallpaper.home.categoryBox', [
                        'title'     => 'Hình nền điện thoại mới',
                        'products'  => $newProducts,
                        'tagBox'    => 'new' // tagBox để tính năng view ảnh của cùng 1 sản phẩm vẫn hoạt động trên tất cả các box
                    ])
                </div> --}}
                <div class="contentBox">
                    <div class="categoryBox">
                        <div class="categoryBox_title">
                            <h2>Hình nền điện thoại mới</h2>
                        </div>
                        <div class="categoryBox_box">
                            <div class="wallpaperGridBox">
                                @foreach($newProducts as $product)
                                    @include('wallpaper.template.wallpaperItem', [
                                        'product'   => $product,
                                        'tagBox'    => 'new' // tagBox để tính năng view ảnh của cùng 1 sản phẩm vẫn hoạt động trên tất cả các box
                                    ])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <!-- === END:: Product Box === -->
                
            <!-- === START:: Product Box === -->
            {{-- @if(!empty($promotionProducts)&&$promotionProducts->isNotEmpty()) --}}
                <div class="contentBox">
                    <!-- load more -->
                    <input type="hidden" id="js_loadMore_total" name="total" value="{{ $totalPromotionProduct ?? 0 }}" />
                    <input type="hidden" id="js_loadMore_loaded" name="loaded" value="{{ $promotionProducts->count() }}" />
                    @include('wallpaper.home.categoryBox', [
                        'title'     => '<a href="/hinh-nen-dien-thoai-khuyen-mai" title="hình nền điện thoại khuyến mãi">Hình nền điện thoại khuyến mãi<i class="fa-solid fa-angle-right" style="margin-left:15px;font-size:15px;"></i></a>',
                        'products'  => $promotionProducts,
                        'tagBox'    => 'promotion'
                    ])
                </div>
            {{-- @endif --}}
            <!-- === END:: Product Box === -->

            {{-- <!-- === START:: Product Box === -->
            <div class="contentBox">
                @include('wallpaper.home.categoryBox', [
                    'title'     => 'Hình nền điện thoại bán chạy',
                    'products'  => $hotProducts,
                    'tagBox'    => 'bestSeller'
                ])
            </div>
            <!-- === END:: Product Box === -->     --}}

        </div>
@endsection
@push('modal')

@endpush
@push('bottom')
    <!-- Header bottom -->
    @include('wallpaper.snippets.headerBottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('wallpaper.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        $(window).ready(function(){
            /* load more lần đầu nếu nằm trong vùng xem */
            loadWallpaperPromotionMore();
            $(window).on('scroll', function() {
                loadWallpaperPromotionMore(); 
            });            
        })
        /* loadmore wallpaper */
        function loadWallpaperPromotionMore(requestLoad = 5){
            var boxCategory       = $('#js_loadMore_box');
            if(boxCategory.length&&!boxCategory.hasClass('loading')){
                const distanceLoad  = boxCategory.outerHeight() + boxCategory.offset().top;
                if($(window).scrollTop() + 1200 > boxCategory.outerHeight() + boxCategory.offset().top) {
                    /* thực thi */
                    /* thêm class để đánh dấu đăng load => không load nữa */
                    boxCategory.addClass('loading');
                    /* lấy dữ liệu */
                    const total         = parseInt($('#js_loadMore_total').val());
                    const loaded        = parseInt($('#js_loadMore_loaded').val());
                    // const keyCategory   = $('#js_loadMore_keyCategory').val();
                    if(total>loaded){
                        $.ajax({
                            url         : '{{ route("main.category.loadMorePromotion") }}',
                            type        : 'get',
                            dataType    : 'json',
                            data        : {
                                total           : total,
                                loaded          : loaded,
                                request_load    : requestLoad
                            },
                            success     : function(response){
                                /* xóa bỏ class để thể hiện đã load xong */
                                boxCategory.removeClass('loading');
                                /* append dữ liệu */
                                if(response.content!=''){
                                    $('#js_loadMore_loaded').val(response.loaded);
                                    boxCategory.append(response.content);
                                }
                            }
                        });
                    }

                }
            }
        }
    </script>
@endpush