<div class="sortBox">
    <div class="sortBox_left">
        <!-- chế độ xem -->
        <div id="selectCustom_viewBy" class="selectCustom">
            <!-- hidden -->
            <input type="hidden" name="view_by" value="" />
            <div class="selectCustom_text">
                {{ empty($language)||$language=='vi' ? 'Duyệt theo' : 'Browse by' }}
            </div>
            <div class="selectCustom_input">
                <!-- load Ajax -->
                @php
                    $dataView   = config('main.view_by');
                    $nameViewBy = '--';
                    if(!empty($viewBy)) $nameViewBy = $dataView[$viewBy]['icon'].$dataView[$viewBy]['name'];
                @endphp
                {!! $nameViewBy !!}
            </div>
            <div class="selectCustom_box">
                @foreach($dataView as $key => $value)
                    @php
                        $selected = null;
                        if(!empty($viewBy)&&$key==$viewBy) $selected = 'selected';
                    @endphp
                    <div class="selectCustom_box_item {{ $selected }}" data-value-view-by="{{ $key }}" onClick="changeViewBy(this);">
                        @if($language=='vi')
                            {!! $value['icon'].$value['name'] !!}
                        @else 
                            {!! $value['icon'].$value['en_name'] !!}
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <!-- sort by -->
        <div class="selectCustom hide-990">
            <div class="selectCustom_text">
                Sắp xếp theo
            </div>
            <div class="selectCustom_input">
                <i class="fa-solid fa-star"></i>Đề xuất
            </div>
            <div class="selectCustom_box">
                <div class="selectCustom_box_item selected" data-value-sort-by="">
                    <i class="fa-solid fa-star"></i>Đề xuất
                </div>
            </div>
        </div>
        <!-- Chủ đề -->
        <div class="selectCustom hide-990">
            <div class="selectCustom_text">
                {{ empty($language)||$language=='vi' ? 'Chủ đề' : 'Topic' }}
            </div>
            <div class="selectCustom_input">
                @if(empty($language)||$language=='vi')
                    @if(!empty($categoryChoose->seo)&&$categoryChoose->seo->title!='Hình nền điện thoại')
                        {{ $categoryChoose->seo->title }}
                    @else 
                        Tất cả
                    @endif
                @else
                    @if(!empty($categoryChoose->seo)&&$categoryChoose->en_seo->title!='Phone Wallpaper')
                        {{ $categoryChoose->en_seo->title }}
                    @else 
                        All
                    @endif
                @endif
            </div>
            <div class="selectCustom_box">
                @if(empty($language)||$language=='vi')
                    <a href="{{env('APP_URL') }}/hinh-nen-dien-thoai" class="selectCustom_box_item {{ !empty($categoryChoose->seo)&&$categoryChoose->seo->title!='Hình nền điện thoại' ? '' : 'selected' }}">
                        Tất cả
                    </a>
                @else 
                    <a href="{{env('APP_URL') }}/phone-wallpapers" class="selectCustom_box_item {{ !empty($categoryChoose->en_seo)&&$categoryChoose->en_seo->title!='Phone Wallpaper' ? '' : 'selected' }}">
                        All
                    </a>
                @endif
                @if(!empty($categories)&&$categories->isNotEmpty())
                    @foreach($categories as $category)
                        @if($category->seo->title!='Hình nền điện thoại'&&$category->en_seo->title!='Phone Wallpaper')
                            @php
                                $selected = '';
                                if(!empty($categoryChoose->id)&&$categoryChoose->id==$category->id) $selected = 'selected';
                            @endphp
                            @if(empty($language)||$language=='vi')
                                <a href="{{ env('APP_URL') }}/{{ $category->seo->slug_full }}" class="selectCustom_box_item {{ $selected }}">
                                    {{ $category->seo->title }}
                                </a>
                            @else   
                                <a href="{{ env('APP_URL') }}/{{ $category->en_seo->slug_full }}" class="selectCustom_box_item {{ $selected }}">
                                    {{ $category->en_seo->title }}
                                </a>
                            @endif
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
        <!-- Phong cách -->
        <div class="selectCustom hide-990">
            <div class="selectCustom_text">
                {{ empty($language)||$language=='vi' ? 'Phong cách' : 'Style' }}
            </div>
            <div class="selectCustom_input">
                @if(empty($language)||$language=='vi')
                    {{ !empty($styleChoose->seo) ? $styleChoose->seo->title : 'Tất cả' }}
                @else 
                    {{ !empty($styleChoose->seo) ? $styleChoose->en_seo->title : 'All' }}
                @endif
            </div>
            <div class="selectCustom_box">
                @if(empty($language)||$language=='vi')
                    <a href="{{env('APP_URL') }}/hinh-nen-dien-thoai" class="selectCustom_box_item {{ !empty($styleChoose->id) ? '' : 'selected' }}">
                        Tất cả
                    </a>
                @else 
                    <a href="{{env('APP_URL') }}/phone-wallpapers" class="selectCustom_box_item {{ !empty($styleChoose->id) ? '' : 'selected' }}">
                        All
                    </a>
                @endif
                @if(!empty($styles)&&$styles->isNotEmpty())
                    @foreach($styles as $style)
                        @php
                            $selected = '';
                            if(!empty($styleChoose->id)&&$styleChoose->id==$style->id) $selected = 'selected';
                        @endphp
                        @if(empty($language)||$language=='vi')
                            <a href="{{ env('APP_URL') }}/{{ $style->seo->slug_full }}" class="selectCustom_box_item {{ $selected }}">
                                {{ $style->seo->title }}
                            </a>
                        @else 
                            <a href="{{ env('APP_URL') }}/{{ $style->en_seo->slug_full }}" class="selectCustom_box_item {{ $selected }}">
                                {{ $style->en_seo->title }}
                            </a>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
        <!-- Sự kiện -->
        <div class="selectCustom hide-990">
            <div class="selectCustom_text">
                {{ empty($language)||$language=='vi' ? 'Sự kiện' : 'Event' }}
            </div>
            <div class="selectCustom_input">
                @if(empty($language)||$language=='vi')
                    {{ !empty($eventChoose->seo) ? $eventChoose->seo->title : 'Tất cả' }}
                @else 
                    {{ !empty($eventChoose->seo) ? $eventChoose->en_seo->title : 'All' }}
                @endif
            </div>
            <div class="selectCustom_box">
                @if(empty($language)||$language=='vi')
                    <a href="{{env('APP_URL') }}/hinh-nen-dien-thoai" class="selectCustom_box_item {{ !empty($eventChoose->id) ? '' : 'selected' }}">
                        Tất cả
                    </a>
                @else 
                    <a href="{{env('APP_URL') }}/phone-wallpapers" class="selectCustom_box_item {{ !empty($eventChoose->id) ? '' : 'selected' }}">
                        All
                    </a>
                @endif
                @if(!empty($events)&&$events->isNotEmpty())
                    @foreach($events as $event)
                        @php
                            $selected = '';
                            if(!empty($eventChoose->id)&&$eventChoose->id==$event->id) $selected = 'selected';
                        @endphp
                        @if(empty($language)||$language=='vi')
                            <a href="{{ env('APP_URL') }}/{{ $event->seo->slug_full }}" class="selectCustom_box_item {{ $selected }}">
                                {{ $event->seo->title }}
                            </a>
                        @else 
                            <a href="{{ env('APP_URL') }}/{{ $event->en_seo->slug_full }}" class="selectCustom_box_item {{ $selected }}">
                                {{ $event->en_seo->title }}
                            </a>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div class="sortBox_right">
        <div class="sortBox_right_item">
            @if(empty($language)||$language=='vi')
                <span id="js_filterProduct_count" class="highLight">
                    {{ $viewBy=='set' ? $totalSet : $totalWallpaper }}
                </span> {{ $viewBy=='set' ? 'bộ hình nền' : 'hình nền' }}
            @else 
                <span id="js_filterProduct_count" class="highLight">
                    {{ $viewBy=='set' ? $totalSet : $totalWallpaper }}
                </span> {{ $viewBy=='set' ? 'set wallpapers' : 'wallpapers' }}
            @endif
        </div>
    </div>
</div>

@pushonce('scriptCustom')
    <script type="text/javascript">
        
        function setValueViewBy(idSelect) {
            var childFirst          = $('#' + idSelect + ' .selectCustom_box .selectCustom_box_item:first');
            var textSelected        = childFirst.html();
            var valueSelected       = childFirst.data('value-view-by');
            $('#'+idSelect+' .selectCustom_box').children().each(function(){
                if($(this).hasClass('selected')){
                    textSelected    = $(this).html();
                    valueSelected   = $(this).data('value-view-by');
                }
            })
            $('#'+idSelect).find(".selectCustom_input").html(textSelected);
            $('#'+idSelect).find("input").val(valueSelected);
        }

        function changeViewBy(inputElement){
            var element             = $(inputElement);
            /* nếu click đối tượng đang được chọn thì không làm gì cả */
            if(!element.hasClass('selected')){
                // Lấy nội dung của mục đã chọn
                var selectedValue   = element.data('value-view-by');
                // Cập nhật nội dung của .selectCustom_input
                element.closest(".selectCustom").find("input").val(selectedValue);
                $('#formViewBy').submit();
            }
        }
        
    </script>
@endpushonce