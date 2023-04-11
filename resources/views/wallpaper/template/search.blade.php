
<div class="searchViewBefore">
    <div class="searchViewBefore_input">
        <!-- value = null không lưu giá trị search cũ -->
        <input id="searchProductAjax_input" type="text" name="key_search" placeholder="Tìm kiếm" value="" onkeyup="searchProductAjax(this)" autocomplete="off" />
        
        <button type="button" class="button" aria-label="tìm kiếm sản phẩm">
            <img type="submit" src="{{ Storage::url('images/svg/search.svg') }}" alt="tìm kiếm hình nền điện thoại" title="tìm kiếm hình nền điện thoại" />
        </button>
    </div>
    <div id="js_searchProductAjax_idWrite" class="searchViewBefore_selectbox">
        <div class="searchViewBefore_selectbox_item">
            <div>
                <img src="/storage/images/svg/icon-search-100.png" alt="Tìm kiếm sản phẩm trên {{ config('main.company_name') }}" title="Tìm kiếm sản phẩm trên {{ config('main.company_name') }}" style="width:120px;margin:0 auto;" />
                {{-- <div>Nhập tìm kiếm của bạn!</div> --}}
            </div>
        </div>
    </div>
    <div class="searchViewBefore_background" onClick="closeBoxSearchMobile();"></div>
</div>
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
    </script>
@endpush
