@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Product Schema -->
    @php
        $currency   = empty($language)||$language=='vi' ? 'VND' : 'USD';
        $lowPrice   = 0;
        $highPrice  = 0;
    @endphp
    @include('wallpaper.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice, 'currency' => $currency])
    <!-- END:: Product Schema -->

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.social', compact('item', 'lowPrice', 'highPrice'))
    <!-- END:: Title - Description - Social -->

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.breadcrumb', compact('breadcrumb'))
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

    {{-- <!-- STRAT:: FAQ Schema -->
    @include('wallpaper.schema.itemlist', ['data' => $products])
    <!-- END:: FAQ Schema -->

    <!-- STRAT:: ImageObject Schema -->
    @php
        $dataImages = new \Illuminate\Database\Eloquent\Collection;
        foreach($products as $product){
            foreach($product->prices as $price){
                foreach($price->wallpapers as $wallpaper) {
                    $dataImages[] = $wallpaper->infoWallpaper;
                }
            }
        }
    @endphp
    @include('wallpaper.schema.imageObject', ['data' => $dataImages])
    <!-- END:: ImageObject Schema --> --}}

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
        <div class="contentBox">
            <div style="display:flex;">
                @php
                    if(empty($language)||$language=='vi'){
                        $titlePage = $item->seo->slug=='hinh-nen-dien-thoai' ? $item->name : 'Hình nền điện thoại '.$item->name;
                    }else {
                        $titlePage = $item->en_seo->slug=='photo-beautiful-girl' ? $item->en_name : $item->en_name.' Phone Wallpapers';
                    }
                @endphp
                <h1>{{ $titlePage }}</h1>
                <!-- từ khóa vừa search -->
                @if(!empty(request('search')))
                    <div class="keySearchBadge">
                        <div class="keySearchBadge_label">
                            - tìm kiếm với:
                        </div>
                        <div class="keySearchBadge_box">
                            <div class="keySearchBadge_box_item">
                                <div class="keySearchBadge_box_item_badge">
                                    <div>{{ request('search') }}</div>
                                    <a href="{{ URL::current() }}" class="keySearchBadge_box_item_badge_action"><i class="fa-solid fa-xmark"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- load more -->
            <input type="hidden" id="total" name="total" value="{{ $total }}" />
            <input type="hidden" id="loaded" name="loaded" value="{{ $loaded ?? 0 }}" />
            <input type="hidden" id="topLoad" name="topLoad" value="" />
            <div class="freeWallpaperBox">
                @foreach($wallpapers as $wallpaper)
                    @include('wallpaper.free.item', compact('wallpaper', 'language'))
                @endforeach
            </div>

        </div>
        <!-- Nội dung -->
        @if(!empty($content))
            <div id="js_buildTocContentMain_element" class="contentElement contentBox maxContent-1200">
                <div id="tocContentMain"></div>
                {!! $content !!}
            </div>
        @endif
        
    </div>
@endsection
@push('modal')

@endpush
@push('bottom')
    <!-- Header bottom -->
    @include('wallpaper.snippets.headerBottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        $(window).ready(function(){
            /* lazyload */ 
            lazyload();
            /* load more */
            loadFreeWallpaperMore(50);
            $(window).scroll(function(){
                loadFreeWallpaperMore(50);
            })
            /* tính lại khi resize */
            $(window).resize(function(){
                setViewAllImage();
            })
        })

        function setViewAllImage(dataJson){
            const box = $('.freeWallpaperBox');
            /* tính số cột đang hiển thị */ 
            const row = calculateColumns();
            /* duyệt và set vị trí */
            var dataColumn          = {};
            box.children().each(function(index) {
                const idWallpaper = $(this).data('id');
                /* set vị trí của từng ảnh */
                const domItem   = $('#js_calculatorPosition_item_'+idWallpaper);
                if(index>=row) setViewPerImage(domItem, index, row);
                /* tính tổng chiều cao của box */
                const itemHeight = domItem.outerHeight(true);
                const columnIndex = index % row;
                if (!dataColumn[columnIndex]) dataColumn[columnIndex] = 0;
                dataColumn[columnIndex] += parseInt(itemHeight + 16);
            });
            /* set height cho toàn box -> lấy height của cột cao nhất (vì các phần tử con position: absolute; nên phải lấy theo cách này) */
            const maxHeight = Math.round(Math.max(...Object.values(dataColumn)));
            $('.freeWallpaperBox').css('min-height', maxHeight+'px');
            /* set khi load gần đến cột ngắn nhất load tiếp */ 
            const minHeight = Math.round(Math.min(...Object.values(dataColumn)));
            $('#topLoad').val(minHeight);
        }

        function setViewPerImage(domItem, indexItem, row) {
            const positionItem  = domItem.position();
            const parent        = $(domItem).parent();
            // Tìm domItemTop cách domItem 3 phần tử về phía trên
            const index         = domItem.index();
            const domItemTop    = parent.children().eq(index - row);
            const domItemBefore = domItem.prev();
            if (domItemTop.length > 0) {
                // Lấy vị trí của domItemTop
                const positionTop       = domItemTop.position();
                const positionBefore    = domItemBefore.position();
                // Thiết lập lại vị trí của domItem
                const newPositionTop    = positionTop.top + 16 + domItemTop.height();
                var newPositionleft     = positionBefore.left + 15 + domItemBefore.width();
                if(indexItem%row==0) newPositionleft = 0;
                domItem.css({
                    'position': 'absolute',
                    'top': newPositionTop + 'px',
                    'left': newPositionleft + 'px',
                    'width': domItemTop.width() + 'px',
                });
            }
        }

        function calculateColumns() {
            // Lấy chiều rộng của .freeWallpaperBox (bao gồm cả margin và padding)
            const boxWidth = $('.freeWallpaperBox').outerWidth();
            // Lấy kích thước mỗi cột trong grid
            const columnSize = $('.freeWallpaperBox').css('grid-template-columns').match(/\d+px/g);
            // Tính toán số cột hiển thị
            let totalColumns = 0;
            columnSize.forEach(size => {
                if (parseInt(size) <= boxWidth) {
                totalColumns++;
                }
            });
            return totalColumns;
        }

        /* loadmore wallpaper */
        function loadFreeWallpaperMore(requestLoad = 20){

            console.log(123);
            var boxCategory         = $('.freeWallpaperBox');
            const total             = $('#total').val();
            const loaded            = $('#loaded').val();
            
            if(boxCategory.length&&!boxCategory.hasClass('loading')&&parseInt(total)>parseInt(loaded)){
                
                const distanceLoad  = $('#topLoad').val();;
                if($(window).scrollTop() + 1500 > distanceLoad) {
                    /* thực thi */
                    /* thêm class để đánh dấu đăng load => không load nữa */
                    boxCategory.addClass('loading');
                    /* lấy dữ liệu */
                    const arrayIdProduct    = $('#js_loadMore_array_product_info_id').val();
                    const language          = $('#language').val();
                    const view_by           = $('#js_loadMore_view_by').val();
                    $.ajax({
                        url         : '{{ route("admin.freeWallpaper.loadmoreFreeWallpapers") }}',
                        type        : 'get',
                        dataType    : 'json',
                        data        : {
                            total, loaded, requestLoad
                        },
                        success     : function(response){
                            /* xóa bỏ class để thể hiện đã load xong */
                            boxCategory.removeClass('loading');
                            /* append dữ liệu */
                            $('#loaded').val(response.loaded);
                            if(response.content!='') {
                                boxCategory.append(response.content);
                            }
                            // Kiểm tra khi tất cả các phần tử đã được load xong
                            waitForImagesLoaded(boxCategory, function () {
                                setViewAllImage();
                            });
                        }
                    });
                }
            }
        }
        // Hàm kiểm tra khi tất cả các hình ảnh trong boxCategory đã được load xong
        function waitForImagesLoaded(boxCategory, callback) {
            var images = boxCategory.find('img');
            var imagesToLoad = images.length;

            if (imagesToLoad === 0) {
                callback();
            }

            images.on('load', function () {
                imagesToLoad--;
                if (imagesToLoad === 0) {
                    callback();
                }
            }).each(function () {
                // Trigger sự kiện load để xử lý trường hợp ảnh đã được cache
                if (this.complete || this.complete === undefined) {
                    var src = this.src;
                    // Thiết lập src vào không gì để tránh lỗi caching
                    this.src = '#';
                    this.src = src;
                }
            });
        }

    </script>
@endpush