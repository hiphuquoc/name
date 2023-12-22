<!-- load more -->
<input type="hidden" id="js_loadMore_total" name="load_more_total" value="{{ $total }}" />
<input type="hidden" id="js_loadMore_loaded" name="load_more_loaded" value="{{ $loaded }}" /> 
<input type="hidden" id="js_loadMore_id" name="load_more_id" value="{{ $id }}" />  
<input type="hidden" id="js_loadMore_type" name="load_more_type" value="{{ $type }}" /> 
<input type="hidden" id="js_loadMore_key_search" name="load_more_key_search" value="{{ $search ?? '' }}" />
<!-- box -->
<div id="js_loadMore_box" class="wallpaperGridBox">
    @if(!empty($products)&&$products->isNotEmpty())

        @if(!empty($viewBy)&&$viewBy=='set')
            @foreach($products as $product)
                @php
                    if($loop->index>4) break;
                    $lazyload   = false;
                    if($loop->index>4) $lazyload   = true;
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
                    if($loop->index>4) $lazyload   = true;
                @endphp
                @foreach($product->prices as $prices)
                    @foreach($prices->wallpapers as $wallpaper)
                        @include('wallpaper.template.perWallpaperItem', [
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
        function loadWallpaperMore(requestLoad = 5){
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
                    const id            = $('#js_loadMore_id').val();
                    const type          = $('#js_loadMore_type').val();
                    const search        = $('#js_loadMore_search').val();
                    const language      = $('#language').val();
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