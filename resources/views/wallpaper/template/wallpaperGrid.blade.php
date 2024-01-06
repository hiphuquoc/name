<!-- load more -->
<input type="hidden" id="js_loadMore_total" value="{{ $total }}" />
<input type="hidden" id="js_loadMore_loaded" value="{{ $loaded }}" /> 
<input type="hidden" id="js_loadMore_id" value="{{ $id }}" />  
<input type="hidden" id="js_loadMore_type" value="{{ $type }}" /> 
<input type="hidden" id="js_loadMore_key_search" value="{{ $search ?? '' }}" />
<input type="hidden" id="js_loadMore_view_by" value="{{ Cookie::get('view_by') ?? 'set' }}" />
<!-- box -->
<div id="js_loadMore_box" class="wallpaperGridBox">
    @if(!empty($products)&&$products->isNotEmpty())
        @php
            $productShow = $loaded; /* số sản phẩm muốn in ra ở lần đầu tiên */
        @endphp
        @if(!empty($viewBy)&&$viewBy=='set')
            @foreach($products as $product)
                @php
                    $lazyload   = false;
                    if($loop->index>=$productShow) {
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
                    if($loop->index>=$productShow) {
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
    @if(!$products->isNotEmpty()&&!empty($contentEmpty))
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
                    const total         = parseInt($('#js_loadMore_total').val());
                    const loaded        = parseInt($('#js_loadMore_loaded').val());
                    const id            = $('#js_loadMore_id').val();
                    const type          = $('#js_loadMore_type').val();
                    const search        = $('#js_loadMore_search').val();
                    const language      = $('#language').val();
                    const view_by       = $('#js_loadMore_view_by').val();
                    if(total>loaded){
                        $.ajax({
                            url         : '{{ route("main.category.loadMore") }}',
                            type        : 'get',
                            dataType    : 'json',
                            data        : {
                                total,
                                loaded,
                                id,
                                type,
                                search,
                                language,
                                view_by,
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