@extends('layouts.wallpaper')
@push('headCustom')
@section('content')
    <div style="overflow:hidden;">
        <div class="contentBox">
            <div class="container">
                @php
                    $count      = 0;
                    foreach($order->products as $product){
                        $count  += $product->infoPrice->sources->count();
                    }
                @endphp
                
                <h1>Tải hình ảnh ({{ $count }})</h1>
                <div class="pageCartBox">
                    <div id="js_checkEmptyCart_idWrite" class="pageCartBox_left">

                        {{-- <div class="cartSectionBox">
                            <div class="cartSectionBox_body">
                                <div class="cartProductBox_head">
                                    <div>Thông tin</div>
                                    <div>Tải ảnh</div>
                                </div>
                                <div class="cartProductBox_body">
                                    @foreach($order->products as $product)
                                        @foreach($product->infoPrice->sources as $source)
                                            <div id="js_downloadSource_{{ $source->id }}" class="cartProductBox_body_item">
                                                <div class="cartProductBox_body_item_info">
                                                    <div class="cartProductBox_body_item_info_content" style="margin-left:0;">
                                                        <div class="cartProductBox_body_item_info_content_title" style="font-weight:normal;font-family:'SVN-Gilroy', tahoma, serif;">
                                                            {{ $product->infoProduct->name.' ('.($loop->index+1).')' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="cartProductBox_body_item_price">
                                                    <div class="cartProductBox_body_item_price_icon" style="margin-bottom:auto;" onClick="downloadSource({{ $source->id }});">
                                                        <img src="{{ Storage::url('images/svg/download.svg') }}" />
                                                    </div>
                                                </div>
                                            </div>
                            
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div> --}}

                        <div class="wallpaperSourceGrid">
                            @php
                                $i = 0;
                            @endphp
                            @foreach($order->products as $product)
                                @foreach($product->infoPrice->sources as $source)
                                    @php
                                        if($i<3){
                                            $attribute = 'class="wallpaperSourceGrid_item_image" style="background:url(\''.Storage::url($source->file_path).'\') no-repeat;background-size: 100% 100%;"';
                                        }else {
                                            $attribute = 'class="wallpaperSourceGrid_item_image lazyload" data-src="'.Storage::url($source->file_path).'" style="background:url() no-repeat;background-size: 100% 100%;"';
                                        }
                                        
                                    @endphp
                                    <div id="js_downloadSource_{{ $source->id }}" class="wallpaperSourceGrid_item" onClick="downloadSource({{ $source->id }});">
                                        <div {!! $attribute !!}></div>
                                        <div class="wallpaperSourceGrid_item_action">
                                            <img src="{{ Storage::url('images/svg/download.svg') }}" />
                                        </div>
                                        <div class="wallpaperSourceGrid_item_background"></div>
                                    </div>
                                    @php
                                        ++$i;
                                    @endphp
                                @endforeach
                            @endforeach
                        </div>

                    </div>
                    <div class="pageCartBox_right">
                        <div id="js_scrollMenu" class="cartSectionBox">
                            <div class="actionPageConfirm">

                                <div class="actionPageConfirm_item" onClick="downloadSourceAll('{{ $order->code }}');">
                                    <div class="actionPageConfirm_item_icon">
                                        <img src="{{ Storage::url('images/svg/download-success.svg') }}" />
                                    </div>
                                    <div class="actionPageConfirm_item_text">
                                        Tải tất cả (.zip)
                                    </div>
                                </div>
                                {{-- <div class="actionPageConfirm_item" onClick="downloadSourceAll('{{ $order->code }}');">
                                    <div class="actionPageConfirm_item_icon">
                                        <img src="{{ Storage::url('images/svg/download-success.svg') }}" />
                                    </div>
                                    <div class="actionPageConfirm_item_text">
                                        Tải tất cả (.zip)
                                    </div>
                                </div> --}}

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('modal')
    <div id="js_toggleWaiting_box" class="waitingBox">
        <div class="waitingBox_box">

            <div id="loading_1">
                <img class="loading_1" src="{{ config('image.loading_main_css') }}" alt="loading cart" title="loading cart" />
            </div>
            <div class="waitingBox_box_text">Đang nén ảnh...</div>

        </div>
        <div class="waitingBox_bg"></div>
    </div>
@endpush
@push('bottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.6.0/jszip.min.js"></script>
    <script type="text/javascript">

        $(window).ready(function(){
            fixedElement();
        });

        function fixedElement(){
            var elementOffset   = $("#js_scrollMenu").offset().top;
            var elementWidth    = $("#js_scrollMenu").outerWidth();
            $(window).scroll(function() {
                var scroll          = $(window).scrollTop();
                if (scroll>=elementOffset&&$(window).width()>1199) {
                    $("#js_scrollMenu").css({
                        position: "fixed", 
                        top: "calc(60px + 2rem)", 
                        width: 'inherit',
                        transition: 'all 0.3s ease-in-out'
                    });
                } else {
                    $("#js_scrollMenu").css({
                        position: "relative", 
                        top: "0", 
                        width: 'inherit', 
                        transform: "translateY(0)"
                    });
                }
            });
        }

        function toggleWaiting(idElement){
            const element   = $('#'+idElement);
            const displayE  = element.css('display');
            if(displayE=='none'){
                /* hiển thị */
                element.css('display', 'flex');
                $('body').css('overflow', 'hidden');
                $('#js_openCloseModal_blur').addClass('blurBackground');
            }else {
                element.css('display', 'none');
                $('body').css('overflow', 'unset');
                $('#js_openCloseModal_blur').removeClass('blurBackground');
            }
        }

        function downloadSource(id) {
            $.ajax({
                url: '{{ route("main.downloadSource") }}',
                type: 'POST',
                dataType: 'json',
                data: {
                    '_token'            : '{{ csrf_token() }}',
                    source_info_id      : id
                },
                success     : function(response){
                    var a = document.createElement("a");
                    a.href      = response.url;
                    a.download  = response.filename;
                    a.click();
                    setTimeout(() => {
                        $('#js_downloadSource_'+id+' .wallpaperSourceGrid_item_image').addClass('alreadyDownload');
                        $('#js_downloadSource_'+id+' .wallpaperSourceGrid_item_action').html('<img src="./storage/images/svg/download-success.svg" />');
                    }, 0);
                }
            });
        }

        function downloadSourceAll(code) {
            /* bật loading */
            toggleWaiting('js_toggleWaiting_box');
            /* thực hiện */
            $.ajax({
                url: '{{ route("main.downloadSourceAll") }}',
                type: 'POST',
                dataType: 'json',
                data: {
                    '_token'    : '{{ csrf_token() }}',
                    'code'      : code
                },
                success: function (response) {
                    // console.log(response);
                    var zip = new JSZip();
                    var promises = [];

                    // Sử dụng biến đếm để đánh số tên file cho từng ảnh
                    var fileIndex = 1;

                    // Duyệt qua từng URL trong mảng response
                    for (var i = 0; i < response.length; i++) {
                        // Thực hiện một yêu cầu fetch để tải file ảnh về
                        var promise = fetch(response[i])
                            .then(function (response) {
                                return response.blob();
                            })
                            .then(function (data) {
                                // Thêm file ảnh vào file zip
                                zip.file("image_" + fileIndex + ".png", data, { binary: true });
                                fileIndex++;
                            })
                            .catch(function (error) {
                                console.log("Error downloading image: " + error);
                            });

                        promises.push(promise);
                    }

                    Promise.all(promises).then(function () {
                        // Tất cả các yêu cầu fetch đã hoàn tất, tiến hành tạo và tải file zip
                        zip.generateAsync({ type: "blob" }).then(function (content) {
                            const url = window.URL.createObjectURL(content);
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;
                            // the filename you want
                            a.download = 'images.zip';
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            /* tắt loading */
                            toggleWaiting('js_toggleWaiting_box');
                        });
                    });
                }
            });
        }
    </script>
@endpush