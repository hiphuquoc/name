<!-- load more -->
<input type="hidden" id="js_loadMoreWallpaper_total" value="{{ $total ?? 0 }}" />
<input type="hidden" id="js_loadMoreWallpaper_loaded" value="{{ $loaded ?? 0 }}" />
@if(!empty($arrayIdCategory))
    <input type="hidden" id="js_loadMoreWallpaper_array_category_info_id" value="{{ json_encode($arrayIdCategory) }}" />
@else 
    <input type="hidden" id="js_loadMoreWallpaper_array_category_info_id" value="[]" />
@endif
<input type="hidden" id="js_loadMoreWallpaper_sort_by" value="{{ $sortBy ?? null }}" />
<input type="hidden" id="js_loadMoreWallpaper_view_by" value="{{ Cookie::get('view_by') ?? 'set' }}" />
<!-- box -->
<div id="js_loadMoreWallpaper_box" class="wallpaperGridBox">
    @if(!empty($wallpapers)&&$wallpapers->isNotEmpty())
        @if(!empty($viewBy)&&$viewBy=='set')
            @foreach($wallpapers as $wallpaper)
                @php
                    $lazyload   = false;
                    if($loop->index>=$loaded) {
                        break;
                    }
                @endphp
                @include('wallpaper.template.wallpaperItem', [
                    'product'   => $wallpaper, 
                    'language'  => $language,
                    'lazyload'  => $lazyload
                ])
            @endforeach
        @else
            @foreach($wallpapers as $wallpaper)
                @php
                    $link           = empty($language)||$language=='vi' ? '/'.$wallpaper->seo->slug_full : '/'.$wallpaper->en_seo->slug_full;
                    $wallpaperName    = $wallpaper->name ?? null;
                    $lazyload       = false;
                    if($loop->index>=$loaded) {
                        break;
                    }
                @endphp
                @foreach($wallpaper->prices as $price)
                    @foreach($price->wallpapers as $wallpaper)
                        @include('wallpaper.template.perWallpaperItem', [
                            'idProduct'   => $wallpaper->id,
                            'idPrice'   => $price->id,
                            'wallpaper' => $wallpaper, 
                            'productName'   => $wallpaperName,
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
    @if(!empty($wallpapers)&&!$wallpapers->isNotEmpty()&&!empty($contentEmpty))
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
            loadMoreWallpaper();
            $(window).on('scroll', function() {
                loadMoreWallpaper();
            });      
        })

        /* loadMoreWallpaper wallpaper */
        function loadMoreWallpaper(requestLoad = 20){
            // Lấy chuỗi query parameters từ URL
            var queryString = window.location.search;
            // Tạo một đối tượng URLSearchParams từ chuỗi query parameters
            var urlParams = new URLSearchParams(queryString);
            // Lấy tất cả các tham số truyền qua URL
            var params = {};
            for (const [key, value] of urlParams) {
                params[key] = value;
            }
            /* lấy dữ liệu */
            var boxCategory         = $('#js_loadMoreWallpaper_box');
            const total             = $('#js_loadMoreWallpaper_total').val();
            const loaded            = $('#js_loadMoreWallpaper_loaded').val();
            if(boxCategory.length&&!boxCategory.hasClass('loading')&&parseInt(total)>parseInt(loaded)){
                const distanceLoad  = boxCategory.outerHeight() + boxCategory.offset().top;
                if($(window).scrollTop()+1500 > boxCategory.outerHeight()+boxCategory.offset().top) {
                    /* thêm class để đánh dấu đăng load => không load nữa */
                    boxCategory.addClass('loading');
                    /* lấy dữ liệu */
                    params.total            = total;
                    params.loaded           = loaded;
                    params.array_category_info_id = $('#js_loadMoreWallpaper_array_category_info_id').val();
                    params.request_load = requestLoad;
                    $.ajax({
                        url         : '{{ route("main.category.loadMoreWallpaper") }}',
                        type        : 'get',
                        dataType    : 'json',
                        data        : params,
                        success     : function(response){
                            /* xóa bỏ class để thể hiện đã load xong */
                            boxCategory.removeClass('loading');
                            /* append dữ liệu */
                            $('#js_loadMoreWallpaper_loaded').val(response.loaded);
                            if(response.content!='') {
                                boxCategory.append(response.content);
                            }
                        }
                    });
                }
            }
        }
    </script>
@endpush