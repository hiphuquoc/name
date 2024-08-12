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
            <div class="contentBox">
                <div class="categoryGrid">
                    <div class="categoryGrid_title">
                        @php
                            $titleBox = '';
                            if($type['key']=='category_info') $titleBox = config('language.'.$language.'.data.wallpaper_by_themes.'.env('APP_NAME'));
                            if($type['key']=='style_info') $titleBox = config('language.'.$language.'.data.wallpaper_by_styles.'.env('APP_NAME'));
                            if($type['key']=='event_info') $titleBox = config('language.'.$language.'.data.wallpaper_by_events.'.env('APP_NAME'));
                        @endphp
                        <h2>{{ $titleBox }}</h2>
                    </div>
                    <div class="categoryGrid_box">
                        @foreach($categories as $category)
                            @foreach($category->seos as $categorySeo)
                                @if(!empty($categorySeo->infoSeo->type)&&$categorySeo->infoSeo->type==$type['key'])
                                    @php
                                        $categoryName           = $categorySeo->infoSeo->title ?? null;
                                        $categoryUrl            = env('APP_URL').'/'.$categorySeo->infoSeo->slug_full;
                                        $categoryThumbMini      = config('image.default');
                                        $categoryThumbSmall     = 'https://namecomvn.storage.googleapis.com/sources/hinh-nen-dien-thoai-canh-tuyet-tuyet-dep-doc-dao-D74HIEKYX1PO895G3ZAB.png';         
                                    @endphp
                                    <div class="categoryGrid_box_item">
                                        <a href="{{ $categoryUrl }}" class="categoryGrid_box_item_image fade">
                                            @if(!empty($category->files)&&$category->files->count()>0)
                                                @foreach($category->files as $file)
                                                    @php
                                                        $categoryThumbMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($file->file_path); 
                                                        $categoryThumbSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($file->file_path); 
                                                        $active             = $loop->index == 0 ? ' active' : '';
                                                    @endphp
                                                    <img class="lazyload {{ $active }}" src="{{ $categoryThumbMini }}" data-src="{{ $categoryThumbSmall }}" alt="{{ $categoryName }}" title="{{ $categoryName }}" />
                                                @endforeach
                                            @else 
                                                <img class="lazyload" src="{{ $categoryThumbMini }}" data-src="{{ $categoryThumbSmall }}" alt="{{ $categoryName }}" title="{{ $categoryName }}" />
                                            @endif
                                        </a>
                                        <div class="categoryGrid_box_item_content">
                                            @if(!empty($category->tags)&&$category->tags->isNotEmpty())
                                                <a href="{{ $categoryUrl }}" class="categoryGrid_box_item_content_title">
                                                    <h2>{{ $categoryName }}</h2>
                                                </a>
                                                <div class="categoryGrid_box_item_content_list">
                                                    @foreach($category->tags as $tag)
                                                        @foreach($tag->infoTag->seos as $tagSeo)
                                                            @if($tagSeo->infoSeo->language==$language)
                                                                <a href="/{{ $tagSeo->infoSeo->slug_full }}">{{ $tagSeo->infoSeo->title }}</a>
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @break
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
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
            let $items = $('.categoryGrid_box_item_image');
            let currentIndex = 1;
            let interval;

            function startCarousel() {
                interval = setInterval(function() {
                    $items.eq(currentIndex).removeClass('active');
                    currentIndex = (currentIndex + 1) % $items.length;
                    $items.eq(currentIndex).addClass('active');
                }, 500); // Thay đổi thời gian giữa các vòng lặp nếu cần
            }

            function stopCarousel() {
                clearInterval(interval);
            }

            $('.categoryGrid_box_item_image').hover(
                function() {
                    startCarousel();
                },
                function() {
                    stopCarousel();
                    $items.removeClass('active'); // Ẩn tất cả các hình ảnh khi không hover
                }
            );

            // Khởi động carousel với hình ảnh đầu tiên
            $items.eq(currentIndex).addClass('active');
        });
    </script>
@endpush