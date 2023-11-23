@php
    if(!empty($language)&&$language=='en'){
        $buttonName = 'Search';
        $labelName  = 'Search phone wallpaper';
    }else {
        $buttonName = 'Tìm kiếm';
        $labelName  = 'Tìm kiếm hình nền điện thoại';
    }
@endphp

<form action="{{ route('main.searchProduct') }}" method="GET">
<div class="searchViewBefore">
    <div class="searchViewBefore_input">
        <!-- value = null không lưu giá trị search cũ -->
        <input id="searchProductAjax_input" type="text" name="key_search" placeholder="{{ $buttonName }}" value="" onkeyup="searchProductAjaxWithDelay(this)" autocomplete="off" />
        <button type="submit" class="button" aria-label="{{ $labelName }}">
            <img src="{{ Storage::url('images/svg/search.svg') }}" alt="" title="{{ $labelName }}" />
        </button>
    </div>
    <div id="js_searchProductAjax_idWrite" class="searchViewBefore_selectbox">
        <div class="searchViewBefore_selectbox_item">
            <div>
                <img src="/storage/images/svg/icon-search-100.png" alt="{{ $labelName }}" title="{{ $labelName }}" style="width:120px;margin:0 auto;" />
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
    </script>
@endpush
