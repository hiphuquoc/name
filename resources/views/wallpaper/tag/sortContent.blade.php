<div class="sortBox">
    <div class="sortBox_left">
        {{-- <!-- chế độ xem -->
        @php
            $titleViewBy    = empty($language)||$language=='vi' ? 'Duyệt theo' : 'Browse by';
            $dataView       = config('main.view_by');
            $viewBy         = Cookie::get('view_by') ?? $dataView[0]['key'];
            $inputViewBy    = null;
            foreach($dataView as $viewItem){
                if($viewBy==$viewItem['key']) {
                    $tmp            = empty($language)||$language=='vi' ? $viewItem['name'] : $viewItem['en_name'];
                    $inputViewBy    = $viewItem['icon'].$tmp;
                }
            }
        @endphp
        <div class="selectCustom">
            <div class="selectCustom_text">
                {!! $titleViewBy !!}
            </div>
            <div class="selectCustom_input maxLine_1">
                {!! $inputViewBy !!}
            </div>
            <div class="selectCustom_box">
                @foreach($dataView as $viewItem)
                    @php
                        $selected = null;
                        if($viewBy==$viewItem['key']) $selected = 'selected';
                    @endphp
                    <div class="selectCustom_box_item {{ $selected }}" onClick="setViewBy('{{ $viewItem['key'] }}')">
                        {!! $viewItem['icon'] !!}{{ empty($language)||$language=='vi' ? $viewItem['name'] : $viewItem['en_name'] }}
                    </div>
                @endforeach
            </div>
        </div> --}}
        <!-- sort by -->
        @php
            $titleSortBy    = empty($language)||$language=='vi' ? 'Sắp xếp theo' : 'Sort by';
            $dataSort       = config('main.sort_type');
            $sortBy         = Cookie::get('sort_by') ?? $dataSort[0]['key'];
            $inputSortBy    = null;
            foreach($dataSort as $sortItem){
                if($sortBy==$sortItem['key']) {
                    $tmp            = empty($language)||$language=='vi' ? $sortItem['name'] : $sortItem['en_name'];
                    $inputSortBy    = $sortItem['icon'].$tmp;
                }
            }
        @endphp
        <div class="selectCustom">
            <div class="selectCustom_text">
                {!! $titleSortBy !!}
            </div>
            <div class="selectCustom_input maxLine_1">
                {!! $inputSortBy !!}
            </div>
            <div class="selectCustom_box">
                @foreach($dataSort as $sortItem)
                    @php
                        $selected = null;
                        if($sortBy==$sortItem['key']) $selected = 'selected';
                    @endphp
                    <div class="selectCustom_box_item {{ $selected }}" onClick="setSortBy('{{ $sortItem['key'] }}')">
                        {!! $sortItem['icon'] !!}{{ empty($language)||$language=='vi' ? $sortItem['name'] : $sortItem['en_name'] }}
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Chủ đề/phong cách/sự kiện -->
        @foreach(config('main.category_type') as $type)
            <div class="selectCustom hide-990">
                @include('wallpaper.tag.selectCustom')
            </div>
        @endforeach
        <!-- icon filter nâng cao -->
        @php
            $titleAdvancedFilter = empty($language)||$language=='vi' ? 'Bộ lọc nâng cao' : 'Advanced filters';
        @endphp
        <div class="filterAdvanced show-990">
            <div id="js_toggleFilterAdvanced_element" class="filterAdvanced_icon" onclick="toggleFilterAdvanced('js_toggleFilterAdvanced_element');">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true" height="18" class="sm:mr-2 sm:text-slate-400"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </div>
            <div class="filterAdvanced_text">
                {{ $titleAdvancedFilter }}
            </div>
            <div class="filterAdvanced_box">
                <div class="filterAdvanced_box_title">
                    <span>{{ $titleAdvancedFilter}}</span>
                    <div class="filterAdvanced_box_title_close" onclick="toggleFilterAdvanced('js_toggleFilterAdvanced_element');">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                </div>
                
                <div class="filterAdvanced_box_content">
                    @foreach(config('main.category_type') as $type)
                        <div class="filterAdvanced_box_content_item">
                            <div class="selectCustom">
                                @include('wallpaper.tag.selectCustom')
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="sortBox_right">
        {{-- <div class="sortBox_right_item">
            <!-- feeling -->
            <div class="feelingBox">
                <div class="feelingBox_item selected" onclick="setFeelingAndSubmit(this);">
                    Tất cả
                    <input type="checkbox" name="search_feeling[]" value="all" /> 
                </div>
                @foreach(config('main.feeling_type') as $feeling)
                    @php
                        $icon       = $feeling['icon_unactive'];
                        $checked    = null;
                        if(!empty($searchFeeling)){
                            foreach($searchFeeling as $f){
                                if($f==$feeling['key']) {
                                    $icon = $feeling['icon'];
                                    $checked = 'checked';
                                    break;
                                }
                            }
                        }
                    @endphp
                    <div class="feelingBox_item" onclick="setFeelingAndSubmit(this);">
                        {!! file_get_contents(public_path($icon)) !!}
                        <input type="checkbox" name="search_feeling[]" value="{{ $feeling['key'] }}" {{ $checked }} /> 
                    </div>
                @endforeach
            </div>
        </div> --}}
        <div class="sortBox_right_item">
            <!-- số lượng -->
            @php
                $nameQuantity = empty($language)||$language=='vi' ? 'Bộ ảnh' : 'Set photos';
                // if($viewBy=='wallpaper') $nameQuantity = empty($language)||$language=='vi' ? 'Ảnh' : 'Photos';
            @endphp
            <span class="quantity maxLine_1">
                {{ $nameQuantity }} {{ $total }}
            </span> 
        </div>
    </div>
</div>