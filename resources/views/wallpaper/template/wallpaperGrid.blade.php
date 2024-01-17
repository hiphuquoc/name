<!-- load more -->
<input type="hidden" id="js_loadMore_array_product_info_id" value="{{ !empty($arrayIdProduct) ? json_encode($arrayIdProduct) : json_encode([]) }}" />
<input type="hidden" id="js_loadMore_view_by" value="{{ Cookie::get('view_by') ?? 'set' }}" />
<!-- box -->
<div id="js_loadMore_box" class="wallpaperGridBox">
    @if(!empty($products)&&$products->isNotEmpty())
        @if(!empty($viewBy)&&$viewBy=='set')
            @foreach($products as $product)
                @php
                    $lazyload   = false;
                    if($loop->index>=$loaded) {
                        break;
                    }
                @endphp
                @include('wallpaper.template.wallpaperItem', [
                    'product'   => $product, 
                    'language'  => $language,
                    'lazyload'  => $lazyload
                ])
            @endforeach
        @else
            @foreach($products as $product)
                @php
                    $link           = empty($language)||$language=='vi' ? '/'.$product->seo->slug_full : '/'.$product->en_seo->slug_full;
                    $productName    = $product->name ?? null;
                    $lazyload       = false;
                    if($loop->index>=$loaded) {
                        break;
                    }
                @endphp
                @foreach($product->prices as $price)
                    @foreach($price->wallpapers as $wallpaper)
                        @include('wallpaper.template.perWallpaperItem', [
                            'idProduct'   => $product->id,
                            'idPrice'   => $price->id,
                            'wallpaper' => $wallpaper, 
                            'productName'   => $productName,
                            'link'      => $link,
                            'language'  => $language,
                            'lazyload'  => $lazyload
                        ])
                    @endforeach
                @endforeach
            @endforeach
        @endif 
    @endif
    <!-- thông báo không có kết quả (cần thiêt) -->
    @if(!empty($products)&&!$products->isNotEmpty()&&!empty($contentEmpty))
        @if(empty($language)||$language=='vi')
            {{ config('main.message.vi.product_empty') }}
        @else 
            {{ config('main.message.en.product_empty') }}
        @endif
    @endif
</div>
@push('scriptCustom')
    <script type="text/javascript">
        $(window).ready(function(){
            /* load more */
            loadWallpaperMore();
            $(window).on('scroll', function() {
                loadWallpaperMore();
            });      
        })

        /* loadmore wallpaper */
        function loadWallpaperMore(requestLoad = 20){
            var boxCategory       = $('#js_loadMore_box');
            if(boxCategory.length&&!boxCategory.hasClass('loading')){
                const distanceLoad  = boxCategory.outerHeight() + boxCategory.offset().top;
                if($(window).scrollTop() + 1500 > boxCategory.outerHeight() + boxCategory.offset().top) {
                    /* thực thi */
                    /* thêm class để đánh dấu đăng load => không load nữa */
                    boxCategory.addClass('loading');
                    /* lấy dữ liệu */
                    const arrayIdProduct    = $('#js_loadMore_array_product_info_id').val();
                    const language          = $('#language').val();
                    const view_by           = $('#js_loadMore_view_by').val();
                    if(arrayIdProduct!='[]'){
                        $.ajax({
                            url         : '{{ route("main.category.loadMore") }}',
                            type        : 'get',
                            dataType    : 'json',
                            data        : {
                                language,
                                view_by,
                                request_load    : requestLoad,
                                array_product_info_id  : arrayIdProduct
                            },
                            success     : function(response){
                                /* xóa bỏ class để thể hiện đã load xong */
                                boxCategory.removeClass('loading');
                                /* append dữ liệu */
                                if(response.content!=''){
                                    $('#js_loadMore_array_product_info_id').val(response.array_product_info_id);
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