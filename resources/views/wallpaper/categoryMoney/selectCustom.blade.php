<div class="selectCustom_text maxLine_1">
    {{ empty($language)||$language=='vi' ? 'Lọc theo '.$type['name'] : 'Filter by '.$type['en_name'] }}
</div>
<div class="selectCustom_input maxLine_1">
    @php
        /* kiểm tra selected với link truy cập hiện tại */
        $flagSelected = false;
        $nameSelect = empty($language)||$language=='vi' ? 'Tất cả' : 'All';
        if(!empty($categoryChoose->seo->type)&&$categoryChoose->seo->type==$type['key']){
            if(!empty($categories)&&$categories->isNotEmpty()){
                foreach($categories as $category){
                    if($category->name==$categoryChoose->name&&$category->flag_show==true) {
                        $nameSelect = empty($language)||$language=='vi' ? $category->name : $category->en_name;
                        $flagSelected = true;
                        break;
                    }
                }
            }
        }
        /* kiểm tra selected với filter */
        if($flagSelected==false){
            if(!empty($filters[$type['key']])){
                if(!empty($categories)&&$categories->isNotEmpty()){
                    foreach($categories as $category){
                        if($filters[$type['key']]==$category->id&&$category->flag_show==true) {
                            $nameSelect = empty($language)||$language=='vi' ? $category->name : $category->en_name;
                            break;
                        }
                    }
                }
            }
        }
    @endphp
    {{ $nameSelect }}
</div>
<div class="selectCustom_box">
    @if(empty($language)||$language=='vi')
        <a href="/{{ $categoryChoose->seo->slug_full ?? null }}" class="selectCustom_box_item">
            Tất cả
        </a>
    @else 
        <a href="/{{ $categoryChoose->en_seo->slug_full ?? null }}" class="selectCustom_box_item">
            All
        </a>
    @endif
    @if(!empty($categories)&&$categories->isNotEmpty())
        <!-- ===== Thuật toán của filter box 

            -> khi truy cập cùng loại thì selectbox loại đó sẽ là thẻ a -> chuyển link
            -> khi filter thêm loại khác thì mới filter

            Ví dụ: sự kiện gồm Tết và Giáng sinh
            - nếu truy cập vào trang tết thì selectbox sự kiện sẽ là thẻ a
            - các selectbox với loại khác như Chủ đề, Phong cách sẽ là filter

        -->
        @foreach($categories as $category)
            @if(!empty($category->seo->type)&&$category->seo->type==$type['key']&&$category->flag_show==true)
                @if(!empty($categoryChoose->seo->type)&&$categoryChoose->seo->type==$type['key'])
                    @php
                        $selected = '';
                        if($categoryChoose->id==$category->id) $selected = 'selected';
                    @endphp
                    @if(empty($language)||$language=='vi')
                        <a href="/{{ $category->seo->slug_full ?? null }}" class="selectCustom_box_item {{ $selected }}" onclick="setFilter(this);">
                            {{ $category->seo->title }}
                            <!-- check box -->
                            <input type="radio" name="filters[{{ $type['key'] }}]" value="{{ $category->id }}" />
                        </a>
                    @else 
                        <a href="/{{ $category->en_seo->slug_full ?? null }}" class="selectCustom_box_item {{ $selected }}" onclick="setFilter(this);">
                            {{ $category->en_seo->title }}
                            <!-- check box -->
                            <input type="radio" name="filters[{{ $type['key'] }}]" value="{{ $category->id }}" />
                        </a>
                    @endif
                @else 
                    @php
                        $selected = '';
                        if(!empty($filters[$type['key']])&&$filters[$type['key']]==$category->id) $selected = 'selected';
                    @endphp
                    <div class="selectCustom_box_item {{ $selected }}" onclick="setFilter(this);">
                        @if(empty($language)||$language=='vi')
                            {{ $category->name }}
                        @else 
                            {{ $category->en_name }}
                        @endif
                        <!-- check box -->
                        <input type="radio" name="filters[{{ $type['key'] }}]" value="{{ $category->id }}" />
                    </div>
                @endif
            @endif
        @endforeach
    @endif
</div>