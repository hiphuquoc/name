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
                <div class="contentBox">
                    @include('wallpaper.home.categoryBox', [
                        'title'     => 'Hình nền điện thoại mới',
                        'products'  => $newProducts,
                        'tagBox'    => 'new' // tagBox để tính năng view ảnh của cùng 1 sản phẩm vẫn hoạt động trên tất cả các box
                    ])
                </div>
            @endif
            <!-- === END:: Product Box === -->
                
            <!-- === START:: Product Box === -->
            @if(!empty($promotionProducts)&&$promotionProducts->isNotEmpty())
                <div class="contentBox">
                    @include('wallpaper.home.categoryBox', [
                        'title'     => '<a href="/hinh-nen-dien-thoai-khuyen-mai" title="hình nền điện thoại khuyến mãi">Hình nền điện thoại khuyến mãi<i class="fa-solid fa-angle-right" style="margin-left:15px;font-size:15px;"></i></a>',
                        'products'  => $promotionProducts,
                        'tagBox'    => 'promotion'
                    ])
                </div>
            @endif
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
    <!-- === START:: Zalo Ring === -->
    {{-- @include('wallpaper.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        
    </script>
@endpush