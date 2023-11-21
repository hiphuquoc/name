<form id="formViewBy" action="{{ route('ajax.settingViewBy') }}" method="GET">
    <div class="sortBox">
        <div class="sortBox_left">
            <!-- chế độ xem -->
            <div id="selectCustom_viewBy" class="selectCustom">
                <!-- hidden -->
                <input type="hidden" name="view_by" value="" />
                <div class="selectCustom_text">
                    {{ empty($language)||$language=='vi' ? 'Duyệt theo:' : 'Browse by:' }}
                </div>
                <div class="selectCustom_input">
                    <!-- load Ajax -->
                </div>
                <div class="selectCustom_box">
                    @foreach(config('main.view_by') as $key => $value)
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
            <!-- Chủ đề -->
            @if(!empty($categories))
                <div class="selectCustom hide-990">
                    {{-- <input type="hidden" name="view_by" value="" /> --}}
                    <div class="selectCustom_text">
                        {{ empty($language)||$language=='vi' ? 'Chủ đề:' : 'Topic:' }}
                    </div>
                    <div class="selectCustom_input">
                        @if(empty($language)||$language=='vi')
                            @if(!empty($categoryChoose)&&$categoryChoose->seo->title!='Hình nền điện thoại')
                                {{ $categoryChoose->seo->title }}
                            @else 
                                Tất cả
                            @endif
                        @else
                            @if(!empty($categoryChoose)&&$categoryChoose->en_seo->title!='Phone Wallpaper')
                                {{ $categoryChoose->en_seo->title }}
                            @else 
                                All
                            @endif
                        @endif
                    </div>
                    <div class="selectCustom_box">
                        @if(empty($language)||$language=='vi')
                            <a href="{{env('APP_URL') }}/hinh-nen-dien-thoai" class="selectCustom_box_item {{ !empty($categoryChoose->id)&&$categoryChoose->seo->title!='Hình nền điện thoại' ? '' : 'selected' }}">
                                Tất cả
                            </a>
                        @else 
                            <a href="{{env('APP_URL') }}/phone-wallpapers" class="selectCustom_box_item {{ !empty($categoryChoose->id)&&$categoryChoose->en_seo->title!='Phone Wallpaper' ? '' : 'selected' }}">
                                All
                            </a>
                        @endif
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
                    </div>
                </div>
            @endif
            <!-- Phong cách -->
            @if(!empty($styles))
                <div class="selectCustom hide-990">
                    <div class="selectCustom_text">
                        {{ empty($language)||$language=='vi' ? 'Phong cách:' : 'Style:' }}
                    </div>
                    <div class="selectCustom_input">
                        @if(empty($language)||$language=='vi')
                            {{ !empty($styleChoose) ? $styleChoose->seo->title : 'Tất cả' }}
                        @else 
                            {{ !empty($styleChoose) ? $styleChoose->en_seo->title : 'All' }}
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
                    </div>
                </div>
            @endif
            <!-- Sự kiện -->
            @if(!empty($events))
                <div class="selectCustom hide-990">
                    <div class="selectCustom_text">
                        {{ empty($language)||$language=='vi' ? 'Sự kiện:' : 'Event:' }}
                    </div>
                    <div class="selectCustom_input">
                        @if(empty($language)||$language=='vi')
                            {{ !empty($eventChoose) ? $eventChoose->seo->title : 'Tất cả' }}
                        @else 
                            {{ !empty($eventChoose) ? $eventChoose->en_seo->title : 'All' }}
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
                    </div>
                </div>
            @endif
        </div>
        <div class="sortBox_right">
            <div class="sortBox_right_item">
                @if(empty($language)||$language=='vi')
                    <span id="js_filterProduct_count" class="highLight">
                        {{ $total }}
                    </span> {{ $viewBy=='set' ? 'bộ hình nền' : 'hình nền' }}
                @else 
                    <span id="js_filterProduct_count" class="highLight">
                        {{ $total }}
                    </span> {{ $viewBy=='set' ? 'set wallpapers' : 'wallpapers' }}
                @endif
            </div>
        </div>
    </div>
</form>
@push('scriptCustom')
    <script type="text/javascript">
        $(document).ready(function () {
            setValueViewBy('selectCustom_viewBy');
        });

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

        // function setHashtagLink(hashtagName, hashtagValue){
        //     // Xây dựng đường dẫn mới với hashtag
        //     var currentUrl  = window.location.href;
        //     // var currentUrl  = "{{ env('APP_URL').'/hinh-nen-dien-thoai' }}";
        //     var newUrl      = updateQueryStringParameter(currentUrl, hashtagName, hashtagValue);
        //     // Chuyển hướng tới đường dẫn mới
        //     window.location.href = newUrl;
        // }

        // function updateQueryStringParameter(uri, key, value) {
        //     // Hàm cập nhật giá trị tham số trong URL
        //     var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        //     var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        //     if (uri.match(re)) {
        //         return uri.replace(re, '$1' + key + "=" + value + '$2');
        //     } else {
        //         return uri + separator + key + "=" + value;
        //     }
        // }
    </script>
@endpush