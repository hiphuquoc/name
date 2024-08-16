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
            @include('wallpaper.home.categoryBox', [
                'type'  => $type['key'],
                'data'  => $categories,
            ])
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
@push('scriptCustom')
    <script type="text/javascript">
        $(document).ready(function() {
            let interval; // Biến lưu trữ interval cho carousel

            // Sử dụng $(document).on để xử lý sự kiện trên các phần tử động
            $(document).on('mouseenter', '.categoryGrid_box_item_image', function() {
                var $children = $(this).children('img');
                let currentIndex = 0;

                // Đổi ảnh ngay lập tức khi hover lần đầu
                $children.eq(currentIndex).removeClass('active');
                currentIndex = (currentIndex + 1) % $children.length;
                $children.eq(currentIndex).addClass('active');

                // Khởi động carousel để tiếp tục đổi ảnh mỗi 2s
                interval = setInterval(function() {
                    $children.eq(currentIndex).removeClass('active');
                    currentIndex = (currentIndex + 1) % $children.length; // Quay lại từ đầu khi hết phần tử
                    $children.eq(currentIndex).addClass('active');
                }, 2000); // Thay đổi thời gian giữa các vòng lặp nếu cần

            }).on('mouseleave', '.categoryGrid_box_item_image', function() {
                clearInterval(interval); // Dừng carousel khi chuột ra ngoài
                var $children = $(this).children('img');
                $children.removeClass('active'); // Ẩn tất cả hình ảnh khi không hover
                $children.eq(0).addClass('active'); // Hiển thị hình ảnh đầu tiên khi không hover
            });

            // Khởi động carousel với hình ảnh đầu tiên khi trang tải hoặc khi có phần tử mới được thêm vào
            function initializeImages() {
                $('.categoryGrid_box_item_image').each(function() {
                    var $children = $(this).children('img');
                    $children.eq(0).addClass('active'); // Hiển thị hình ảnh đầu tiên
                });
            }

            // Khởi động cho các phần tử ban đầu
            initializeImages();

        });
    </script>
@endpush
