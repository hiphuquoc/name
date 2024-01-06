<form method="get" action="{{ route('main.searchProduct') }}">
    <div class="searchViewBefore">
        <div class="searchViewBefore_input">
            <input id="searchProductAjax_input" type="text" name="key_search" placeholder="Tìm kiếm" value="{{ request('key_search') ?? null }}" onkeyup="searchProductAjax(this)" autocomplete="off" />
            <button type="submit" class="button" aria-label="tìm kiếm sản phẩm"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
        <div id="js_searchProductAjax_idWrite" class="searchViewBefore_selectbox">
            <div class="searchViewBefore_selectbox_item">
                <div>
                    <img src="/storage/images/svg/icon-search-100.png" alt="Tìm kiếm sản phẩm trên {{ config('main.company_name') }}" title="Tìm kiếm sản phẩm trên {{ config('main.company_name') }}" style="width:120px;margin:0 auto;" />
                    <div>Nhập tìm kiếm của bạn!</div>
                </div>
            </div>
        </div>
    </div>
</form>