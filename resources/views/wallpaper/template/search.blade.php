@php
    $labelName  = config('language.'.$language.'.data.search_wallpapers');
@endphp

<form action="{{ route('routing', ['slug' => config('language.'.$language.'.slug_page')]).'?search=' }}" method="GET">
    <div class="searchViewBefore">
        <div class="searchViewBefore_input">
            <!-- value = null không lưu giá trị search cũ -->
            <input id="searchProductAjax_input" type="text" name="search" placeholder="{{ $labelName }}" value="" onkeyup="searchProductAjaxWithDelay(this)" autocomplete="off" />
            <button type="submit" class="button" aria-label="{{ $labelName }}">
                <img src="{{ Storage::url('images/svg/search.svg') }}" alt="" title="{{ $labelName }}" />
            </button>
        </div>
        <div id="js_searchProductAjax_idWrite" class="searchViewBefore_selectbox">
            <div class="searchViewBefore_selectbox_item">
                <div>
                    <img src="/storage/images/svg/icon-search-100.png" alt="{{ $labelName }}" title="{{ $labelName }}" style="width:120px;margin:0.75rem auto;" />
                    {{-- <div>Nhập tìm kiếm của bạn!</div> --}}
                </div>
            </div>
        </div>
        <div class="searchViewBefore_background" onClick="closeBoxSearchMobile();"></div>
    </div>
</form>
@push('scriptCustom')
    <script type="text/javascript">
        function toggleSearchMobile(){
            const elementSearchBox = $('.searchViewBefore');
            elementSearchBox.css({
                'opacity'   : '1',
                'width'     : '100%',
                'z-index'   : '1'
            });
            $('.searchViewBefore input').focus();
            /* mở phần hiển thị kết quả search (nếu đang đóng) */
            $('#js_searchProductAjax_idWrite').css('height', 'auto');
            $('.searchViewBefore_background').css('display', 'block');
        }
        function closeBoxSearchMobile(){
            toggleSearchMobile();
            $('.searchViewBefore_selectbox').css('height', '0');
            $('.searchViewBefore_background').css('display', 'none');
        }
        /* mỗi khi người dùng nhập một ký tự mới, hàm searchWallpapersWithDelay sẽ đặt một hẹn giờ (setTimeout) để gọi hàm searchWallpapers sau 0.5 giây. Nếu có thêm ký tự nào được nhập trong khoảng 0.5 giây, hẹn giờ trước đó sẽ bị xóa và hẹn giờ mới sẽ được đặt lại. Điều này giúp tạo ra hiệu ứng chờ giữa các lần nhập. */
        var searchTimer;
        function searchProductAjaxWithDelay(input) {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                searchProductAjax(input);
            }, 500);
        }
        /* tìm kiếm sản phẩm ajax */
        function searchProductAjax(elementButton){
            const valueElement  = $(elementButton).val();
            const language      = $('#language').val();
            $.ajax({
                url         : '{{ route("ajax.searchProductAjax") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    search      : valueElement,
                    language
                },
                success     : function(response){
                    if(response!='') {
                        $('#js_searchProductAjax_idWrite').html(response);
                    }
                    
                }
            });
        }
    </script>
@endpush
