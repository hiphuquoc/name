<!-- load more -->
<input type="hidden" id="js_loadMoreWallpaper_search" value="{{ $search ?? null }}" />
<input type="hidden" id="js_loadMoreWallpaper_total" value="{{ $total ?? 0 }}" />
<input type="hidden" id="js_loadMoreWallpaper_loaded" value="{{ $loaded ?? 0 }}" />
@if(!empty($arrayIdCategory))
    <input type="hidden" id="js_loadMoreWallpaper_array_category_info_id" value="{{ json_encode($arrayIdCategory) }}" />
@else 
    <input type="hidden" id="js_loadMoreWallpaper_array_category_info_id" value="[]" />
@endif
@if(!empty($arrayIdTag))
    <input type="hidden" id="js_loadMoreWallpaper_array_tag_info_id" value="{{ json_encode($arrayIdTag) }}" />
@else 
    <input type="hidden" id="js_loadMoreWallpaper_array_tag_info_id" value="[]" />
@endif
<input type="hidden" id="js_loadMoreWallpaper_sort_by" value="{{ $sortBy ?? null }}" />
{{-- <input type="hidden" id="js_loadMoreWallpaper_view_by" value="{{ Cookie::get('view_by') ?? 'each_set' }}" /> --}}
<!-- box -->
<div id="js_loadMoreWallpaper_box" class="wallpaperGridBox">
    @php
        $loadFirstTime = 10;
    @endphp
    @for($i=1;$i<=$loadFirstTime;++$i)
        <div class="wallpaperGridBox_itemBackground"></div>
    @endfor
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
        var isFirstLoad = true; // Khai báo một biến để theo dõi xem đã load lần đầu tiên chưa
        function loadMoreWallpaper(requestLoad = 20) {
            /* lấy dữ liệu */
            var boxCategory = $('#js_loadMoreWallpaper_box');
            const total = $('#js_loadMoreWallpaper_total').val();
            const loaded = $('#js_loadMoreWallpaper_loaded').val();
            if(boxCategory.length&&!boxCategory.hasClass('loading')){
                if(isFirstLoad){
                    loadMoreWallpaperToController("{{ $loadFirstTime }}", true);
                }else {
                    if(parseInt(total)>parseInt(loaded)){
                        loadMoreWallpaperToController(requestLoad, false);
                    }
                }
            }
        }
        function loadMoreWallpaperToController(requestLoad, firstTime = false){
            /* hiện icon loading */
            loadLoading();
            // Lấy chuỗi query parameters từ URL
            var queryString = window.location.search;
            var urlParams = new URLSearchParams(queryString);
            var params = {};
            for (const [key, value] of urlParams) {
                params[key] = value;
            }
            /* lấy dữ liệu */
            var boxCategory = $('#js_loadMoreWallpaper_box');
            const total = $('#js_loadMoreWallpaper_total').val();
            const loaded = $('#js_loadMoreWallpaper_loaded').val();
            /* thêm class để đánh dấu đang load => không load nữa */
            boxCategory.addClass('loading');
            /* lấy dữ liệu */
            params.search = $('#js_loadMoreWallpaper_search').val();
            params.total = total;
            params.loaded = loaded;
            params.array_category_info_id = $('#js_loadMoreWallpaper_array_category_info_id').val();
            params.array_tag_info_id = $('#js_loadMoreWallpaper_array_tag_info_id').val();
            params.request_load = requestLoad;
            $.ajax({
                url: '{{ route("main.category.loadMoreWallpaper") }}',
                type: 'get',
                dataType: 'json',
                data: params,
                success: function (response) {
                    setTimeout(() => {
                        lazyload();
                    }, 0);
                    /* xóa bỏ class để thể hiện đã load xong */
                    boxCategory.removeClass('loading');
                    /* append dữ liệu */
                    $('#js_loadMoreWallpaper_loaded').val(response.loaded);
                    $('#js_loadMoreWallpaper_total').val(response.total); /* cập nhật lại total do load ajax cache */
                    if(firstTime){
                        boxCategory.html(response.content);
                    }else {
                        boxCategory.append(response.content);
                    }
                    /* thêm thông báo nếu empty */
                    if(boxCategory.children().length==0) boxCategory.html('<div>'+"{{ config('language.'.$language.'.data.no_suitable_results_found') }}"+'</div>');
                    /* tắt icon loading */
                    loadLoading('hide');
                }
            });
            // Đặt isFirstLoad thành false sau lần đầu load
            isFirstLoad = false;
            
        }
    </script>
@endpush