<form id="formViewBy" action="{{ route('ajax.settingViewBy') }}" method="GET">
    <div class="sortBox">
        <div class="sortBox_left">
            <div>
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
        <div class="sortBox_right">
            <div class="sortBox_right_item">
                <div id="selectCustom_viewBy" class="selectCustom">
                    <!-- hidden -->
                    <input type="hidden" name="view_by" value="" />
                    <div class="selectCustom_text">
                        @if($language=='vi')
                            Duyệt theo:
                        @else 
                            Browse by:
                        @endif
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
                            <div class="selectCustom_box_item {{ $selected }}" data-value-view-by="{{ $key }}">
                                @if($language=='vi')
                                    {!! $value['icon'].$value['name'] !!}
                                @else 
                                    {!! $value['icon'].$value['en_name'] !!}
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@push('scriptCustom')
    <script type="text/javascript">
        $(document).ready(function () {
            setValueSelectCustom('selectCustom_viewBy');

            // Bắt sự kiện click trên .selectCustom_box_item
            $(".selectCustom_box_item").click(function () {
                var element         = $(this);
                /* nếu click đối tượng đang được chọn thì không làm gì cả */
                if(!element.hasClass('selected')){
                    // Lấy nội dung của mục đã chọn
                    var selectedText    = element.html();
                    var selectedValue   = element.data('value-view-by');
                    // Cập nhật nội dung của .selectCustom_input
                    $(this).closest(".selectCustom").find(".selectCustom_input").html(selectedText);
                    $(this).closest(".selectCustom").find("input").val(selectedValue);
                    // selected lại đối tượng được click
                    element.parent().children().each(function(){
                        $(this).removeClass('selected');
                    })
                    element.addClass('selected');
                    /* submit form (thêm) */
                    $('#formViewBy').submit();
                }
            });

        });

        function setValueSelectCustom(idSelect) {
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
    </script>
@endpush