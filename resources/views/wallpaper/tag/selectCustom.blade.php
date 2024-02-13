<div class="selectCustom_text">
    {{ empty($language)||$language=='vi' ? 'Lọc theo '.$type['name'] : 'Filter by Category'.$type['en_name'] }}
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
        <a href="/hinh-nen-dien-thoai/hinh-nen-dien-thoai-mien-phi" class="selectCustom_box_item">
            Tất cả
        </a>
    @else 
        <a href="/phone-wallpapers/free-phone-wallpapers" class="selectCustom_box_item">
            All
        </a>
    @endif
    @if(!empty($categories)&&$categories->isNotEmpty())
        @foreach($categories as $category)
            @if(!empty($category->seo->type)&&$category->seo->type==$type['key']&&$category->flag_show==true)
                @if(empty($language)||$language=='vi')
                    <a href="/hinh-nen-dien-thoai/hinh-nen-dien-thoai-mien-phi?{{ $category->seo->type }}={{ $category->id }}" class="selectCustom_box_item {{ $selected }}" onclick="setFilter(this);">
                        {{ $category->seo->title }}
                        <!-- check box -->
                        <input type="radio" name="filters[{{ $type['key'] }}]" value="{{ $category->id }}" />
                    </a>
                @else 
                    <a href="/phone-wallpapers/free-phone-wallpapers?{{ $category->en_seo->type }}={{ $category->id }}" class="selectCustom_box_item {{ $selected }}" onclick="setFilter(this);">
                        {{ $category->en_seo->title }}
                        <!-- check box -->
                        <input type="radio" name="filters[{{ $type['key'] }}]" value="{{ $category->id }}" />
                    </a>
                @endif
            @endif
        @endforeach
    @endif
</div>