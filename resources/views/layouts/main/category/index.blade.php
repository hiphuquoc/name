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
        $lowPrice   = 0;
        $highPrice  = 0;
        foreach($products as $product){
            foreach($product->prices as $price){
                if($price->price>$highPrice) $highPrice = $price->price;
                if($price->price<$lowPrice||$lowPrice==0) $lowPrice = $price->price;
            }
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
        
        @include('main.template.breadcrumb')

        <div class="contentBox">
            <!-- Sản phẩm -->
            <div class="sectionBox">
                <div class="container">
                    @if(!empty($titlePage))
                        <h1 style="display:flex;">
                            <div>{{ $titlePage }}</div>
                            <!-- từ khóa vừa search -->
                            @if(!empty(request('key_search')))
                                <div class="keySearchBadge">
                                    <div class="keySearchBadge_item">
                                        <a href="{{ route('main.searchProduct') }}" class="keySearchBadge_item_badge">
                                            <div>{{ request('key_search') }}</div>
                                            <div class="keySearchBadge_item_badge_action"><i class="fa-solid fa-xmark"></i></div>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </h1>
                    @else 
                        <h1>Sản phẩm {{ $item->name ?? $item->seo->title ?? null }}</h1>
                    @endif
            
                    
                    <!-- Product Box -->
                    <div class="categoryWithFilterBox">
                        <div class="categoryWithFilterBox_box">
                            <!-- banner -->
                            {{-- @include('main.categoryProduct.banner') --}}
                            <!-- Sort Box -->
                            <div class="sortBox">
                                <div class="sortBox_left">
                                    <div><span id="js_filterProduct_count" class="highLight">{{ $products->count() }}</span> sản phẩm</div>
                                </div>
                                <div class="sortBox_right">
                                    <div class="sortBox_right_item">
                                        <div style="min-width:100px;">Sắp xếp theo:</div>
                                        <select style="max-width:100px;">
                                            <option>Mặc định</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="productGridFlexBox">
                                @include('main.template.productGrid', [
                                    'product'       => $product ?? null,
                                    'headingTitle'  => 'h2'
                                ])
                                @include('main.template.productGridLoading')
                                <div id="js_filterProduct_hidden"></div>
                            </div>
            
                        </div>
                        <div class="categoryWithFilterBox_filter">
            
                            @include('main.template.filter', [
                                'categories'        => $categories ?? null,
                                'brands'            => $brands ?? $item
                            ])
            
                        </div>
                    </div>
            
                </div>
            </div>
            <!-- Nội dung -->
            @if(!empty($content))
                <div class="sectionBox">
                    <div class="container withBackground">
                        <div class="categoryWithFilterBox">
                            <div id="js_buildTocContentMain_element" class="categoryWithFilterBox_box contentMoreSeoBox">
                                <div id="tocContentMain"></div>
                                {!! $content !!}
                            </div>
                            <div class="categoryWithFilterBox_filter">
                                @include('main.category.sidebarContent')
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
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
        $(window).ready(function(){
            buildTocContentMain('js_buildTocContentMain_element');
        })
    </script>
@endpush