<div id="filterBox" class="filterBox">
    <!-- danh mục con -->
    @if(!empty($categories)&&$categories->isNotEmpty())
        <div class="filterBox_item">
            <div class="filterBox_item_title">
                Dòng sản phẩm
            </div>
            <div class="filterBox_item_box">
                <div class="filterBox_item_box_item">
                    @if($categories->count()>1)
                        <span class="badge badgePrimary selected" data-filter-type="tat-ca-danh-muc" onClick="filterProduct(this)">Tất cả</span>
                    @endif
                    @foreach($categories as $category)
                        @php
                            $selected = $categories->count()>1 ? null : 'selected';
                        @endphp
                        <span class="badge badgePrimary {{ $selected }}" data-filter-type="{{ $category->seo->slug }}" onClick="filterProduct(this)">{{ $category->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <!-- Nhãn hàng -->
    @if(!empty($brands)&&$brands->isNotEmpty())
        <div class="filterBox_item">
            <div class="filterBox_item_title">
                Nhãn hàng
            </div>
            <div class="filterBox_item_box">
                <div class="filterBox_item_box_item">
                    @if($brands->count()>1)
                        <span class="badge badgePrimary selected" onClick="filterProduct(this)">Tất cả</span>
                    @endif
                    @foreach($brands as $brand)
                        @php
                            $selected = $brands->count()>1 ? null : 'selected';
                        @endphp
                        <span class="badge badgePrimary {{ $selected }}" data-filter-type="{{ $brand->seo->slug }}" onClick="filterProduct(this)">{{ $brand->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <div class="filterBox_item">
        <div class="filterBox_item_title">
            Khoảng giá (VNĐ)
        </div>
        <div class="filterBox_item_box">
            <div class="filterBox_item_box_item">
                <div class="listPrice">
                    <div class="listPrice_item selected" data-filter-price-min="0" data-filter-price-max="99999999999999999999" onClick="filterProduct(this)">Tất cả</div>
                    @foreach(config('main.filter.price') as $price)
                        <div class="listPrice_item" data-filter-price-min="{{ $price['min'] }}" data-filter-price-max="{{ $price['max'] }}" onClick="filterProduct(this)">{{ $price['name'] }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@push('scriptCustom')
    <script type="text/javascript">
        function filterProduct(elementButton){
            /* active button vừa click */
            $(elementButton).parent().children().each(function(){
                $(this).removeClass('selected');
            })
            $(elementButton).addClass('selected');
            /* hiện loading - ẩn child box chính */
            const parent    = $('#js_filterProduct_show');
            const hidden    = $('#js_filterProduct_hidden');
            $('.loadingGridBox').css('display', 'flex');
            parent.children().each(function(){
                $(this).css('display', 'none');
            })
            /* ====== lọc theo key ====== */
            /* lấy partern */
            let type                = [];
            $('#filterBox').find("[data-filter-type]").each(function(){
                if($(this).hasClass('selected')) type.push($(this).data('filter-type'));
            })
            let partern             = new RegExp(type.join('.*'));
            /* tiến hành lọc phần tử theo key */
            let data                = [];
            let dataHidden          = [];
            $(document).find("[data-key]").each(function(){
                if(partern.test($(this).data('key'))){
                    data.push($(this));
                }else {
                    dataHidden.push($(this));
                }
            })
            /* ====== lọc theo giá ====== */
            /* lấy giá trị lọc */
            var valueMin    = 0;
            var valeuMax    = 9999999999999999999999;
            $('#filterBox').find("[data-filter-price-min]").each(function(){
                if($(this).hasClass('selected')){
                    valueMin    = $(this).data('filter-price-min');
                    valeuMax    = $(this).data('filter-price-max');
                }
            })
            /* tiến hành lọc phần từ theo giá */
            let tmp         = [];
            for(let i=0;i<data.length;++i){
                const dataPrice = data[i].data('price');
                if(dataPrice>=valueMin&&dataPrice<=valeuMax) {
                    tmp.push(data[i]);
                }else {
                    dataHidden.push(data[i]);
                }
            }
            data            = tmp;
            /* ẩn loading - hiện lại kết quả */
            setTimeout(() => {
                /* cập nhật lại count */
                $('#js_filterProduct_count').html(data.length);
                /* hiện kết quả */
                /* điền lại kết quả */
                parent.html('');
                for(let i=0;i<data.length;++i){
                    parent.append(data[i].attr('style', '').clone());
                }
                hidden.html('');
                for(let i=0;i<dataHidden.length;++i){
                    hidden.append(dataHidden[i].clone());
                }
                /* nếu trong box chính không có dữ liệu thì thông báo */
                if(parent.html()=='') {
                    parent.append('<div>Không có sản phẩm phù hợp!</div>');
                    
                }
                /* ẩn loading */
                $('.loadingGridBox').css('display', 'none');
            }, 500);
        }

    </script>
@endpush