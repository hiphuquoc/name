@extends('layouts.main')
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
        $lowPrice   = 100000;
        $highPrice  = 1500000;
    @endphp
    @include('main.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Product Schema -->

    <!-- STRAT:: FAQ Schema -->
    @include('main.schema.faq', ['data' => $item->faqs])
    <!-- END:: FAQ Schema -->
<!-- ===== END:: SCHEMA ===== -->
@endpush
@section('content')
    <div style="overflow:hidden;">
        <!-- === START:: Header Top === -->
        @include('main.snippets.headerTop')
        <!-- === END:: Header Top === -->

        <!-- === START:: Header Main === -->
        <!-- === START:: Header Main === -->
        @include('main.cacheHTML.create', [
            'content'   => 'main.snippets.headerMain'
        ])
        <!-- === END:: Header Main === -->

        <!-- === START:: Slider === -->
        @include('main.home.slider')
        <!-- === END:: Slider === -->

        <!-- === START:: Banner Secondary === -->
        @include('main.home.bannerSecondary')
        <!-- === END:: Banner Secondary === -->

        <div class="homeBox">
            <!-- === START:: Banner Secondary === -->
            <div class="sectionBox">
                @include('main.home.categoryGrid', compact('categories'))
            </div>
            <!-- === END:: Banner Secondary === -->

             <!-- === START:: Product Box === -->
             <div class="sectionBox">
                @include('main.home.categoryBox', [
                    'title'     => 'Sản phẩm khuyến mãi',
                    'products'  => $promotionProducts
                ])
            </div>
            <!-- === END:: Product Box === -->   

            <!-- === START:: Product Box === -->
            <div class="sectionBox">
                @include('main.home.categoryBox', [
                    'title'     => 'Sản phẩm mới nhất',
                    'products'  => $newProducts
                ])
            </div>
            <!-- === END:: Product Box === -->

            <!-- === START:: Product Box === -->
            <div class="sectionBox">
                @include('main.home.categoryBox', [
                    'title'     => 'Sản phẩm bán chạy',
                    'products'  => $hotProducts
                ])
            </div>
            <!-- === END:: Product Box === -->            
        </div>

        <!-- === START:: Blog Home Box === -->
        @include('main.home.blogHome')
        <!-- === END:: Blog Home Box === -->
    </div>
@endsection
@push('modal')

@endpush
@push('bottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        
    </script>
@endpush