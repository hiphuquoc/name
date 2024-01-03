@extends('layouts.wallpaper')
@push('headCustom')
@section('content')
    <div style="overflow:hidden;">
        <div class="contentBox">
            <div class="container">
                @php
                    $xhtmlTotal = null;

                @endphp
                <div class="pageCartBox">
                    <div id="js_checkEmptyCart_idWrite" class="pageCartBox_left" style="width:100%;">

                        <div class="confirmMessageBox">
                            <div class="confirmMessageBox_left">
                                <div class="confirmMessageBox_left_icon">
                                    <img src="{{ Storage::url('images/icon-confirm-success.png') }}" />
                                </div>
                                <div class="confirmMessageBox_left_title">
                                    @if($language=='vi')
                                        Thanh toán thành công!
                                    @else 
                                        Payment success!
                                    @endif
                                </div>
                                <div class="confirmMessageBox_left_desc">
                                    @if($language=='vi')
                                        Cảm ơn bạn đã ủng hộ {{ config('main.company_name') }}. Hình ảnh của bạn ở phần bên dưới hoặc bạn có thể truy cập email của mình để tải ảnh.
                                    @else 
                                        Thank you for supporting {{ config('main.company_name') }}. Your images are below or you can access your email to download photos.
                                    @endif
                                </div>
                            </div>
                            <div class="confirmMessageBox_right">
                                <div class="confirmMessageBox_right_item">
                                    @if($language=='vi')
                                        Mã đơn <span class="highLight">{{ $order->code }}</span>
                                    @else 
                                        Code <span class="highLight">{{ $order->code }}</span>
                                    @endif
                                </div>
                                @if(!empty($order->email))
                                    <div class="confirmMessageBox_right_item">
                                        Email: {{ $order->email }}
                                    </div>
                                @endif
                                <div class="confirmMessageBox_right_item">
                                    @if($language=='vi')
                                        {{ $order->paymentMethod->name }} lúc {{ date('H:i\, d/m/Y', strtotime($order->created_at)) }}
                                    @else 
                                        {{ $order->paymentMethod->en_name }} at {{ date('H:i\, d/m/Y', strtotime($order->created_at)) }}
                                    @endif
                                </div>
                                <div class="confirmMessageBox_right_item">
                                    @if($language=='vi')
                                        Tổng thanh toán <span class="price">{!! \App\Helpers\Number::getFormatPriceByLanguage($order->total, $language) !!}</span>
                                    @else 
                                        Total <span class="price">{!! \App\Helpers\Number::getFormatPriceByLanguage($order->total, $language) !!}</span>
                                    @endif
                                </div>
                                <div class="confirmMessageBox_right_item">
                                    @if($language=='vi')
                                        <div>Danh sách sản phẩm trong đơn hàng:</div>
                                    @else 
                                        <div>List of products in the order:</div>
                                    @endif
                                    <ul>
                                        @foreach($order->products as $product)
                                            @php
                                                /* tên product */
                                                $xhtmlProduct           = $language=='vi' ? $product->infoProduct->name : $product->infoProduct->en_name;
                                                /* option product */
                                                if($product->product_price_id=='all'){
                                                    if($language=='vi'){
                                                        $xhtmlOption    = 'Trọn bộ';
                                                    }else {
                                                        $xhtmlOption    = 'Full set';
                                                    }
                                                }else {
                                                    $tmp                = [];
                                                    foreach($product->infoPrice->wallpapers as $wallpaper){
                                                        if($language=='vi'){
                                                            $tmp[]      = $wallpaper->name;
                                                        }else {
                                                            $tmp[]      = $wallpaper->en_name;
                                                        }
                                                    }
                                                    $xhtmlOption        = implode(', ', $tmp);
                                                }
                                            @endphp     
                                            <li>
                                                <div>
                                                    {{ $xhtmlProduct }} ({{ $xhtmlOption }})
                                                </div>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="wallpaperSourceGrid">
                            @foreach($order->products as $product)
                                @if(empty($product->infoPrice))
                                    <!-- trường hợp all -->
                                    @foreach($product->infoProduct->prices as $price)
                                        @foreach($price->wallpapers as $wallpaper)
                                            <div class="wallpaperSourceGrid_item" onClick="downloadSource(this, '{{ $wallpaper->infoWallpaper->file_name }}', '{{ $order->code }}');">
                                                <div class="wallpaperSourceGrid_item_image">
                                                    @php
                                                        /* lấy ảnh mini */
                                                        $imageMini      = \App\Helpers\Image::getUrlImageMiniByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
                                                    @endphp
                                                    <img class="lazyloadSource" src="{{ $imageMini }}" data-order-code="{{ $order->code }}" data-file-name="{{ $wallpaper->infoWallpaper->file_name }}" style="filter:blur(20px);" />
                                                </div>
                                                <div class="wallpaperSourceGrid_item_action">
                                                    <img src="{{ Storage::url('images/svg/download.svg') }}" />
                                                </div>
                                                <div class="wallpaperSourceGrid_item_background"></div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                @else
                                    @foreach($product->infoPrice->wallpapers as $wallpaper)
                                        <div class="wallpaperSourceGrid_item" onClick="downloadSource(this, '{{ $wallpaper->infoWallpaper->file_name }}', '{{ $order->code }}');">
                                            <div class="wallpaperSourceGrid_item_image">
                                                @php
                                                    /* lấy ảnh mini */
                                                    $imageMini      = \App\Helpers\Image::getUrlImageMiniByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
                                                @endphp
                                                <img class="lazyloadSource" src="{{ $imageMini }}" data-order-code="{{ $order->code }}" data-file-name="{{ $wallpaper->infoWallpaper->file_name }}" style="filter:blur(20px);" />
                                            </div>
                                            <div class="wallpaperSourceGrid_item_action">
                                                <img src="{{ Storage::url('images/svg/download.svg') }}" />
                                            </div>
                                            <div class="wallpaperSourceGrid_item_background"></div>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
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
            <div class="waitingBox_box_text">Đang tải ảnh...</div>

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
            // if($("#js_scrollMenu").length) fixedElement();
            // toggleWaiting();
            lazyloadSource();

        });

        function downloadSource(box, fileName, orderCode){
            $.ajax({
                url: "{{ route('ajax.downloadImageSource') }}",
                type: 'get',
                dataType: 'json',
                data: {
                    order_code  : orderCode,
                    file_name   : fileName
                }
            }).done(function (response) {
                if (response.url) {
                    // Sử dụng XMLHttpRequest để tải ảnh dưới dạng blob
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", response.url, true);
                    xhr.responseType = "blob";

                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            var blob = xhr.response;
                            // Tạo một đường dẫn (URL) từ blob
                            var blobUrl = URL.createObjectURL(blob);
                            // Tạo một thẻ a để tải xuống và kích hoạt nó
                            var a = document.createElement("a");
                            a.href = blobUrl;
                            a.download = response.file_name;
                            a.style.display = "none";
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            // Giải phóng đường dẫn blob khi đã sử dụng xong
                            URL.revokeObjectURL(blobUrl);
                        }
                    };
                    xhr.send();
                    /* đánh dấu ảnh đã tải */ 
                    $(box).addClass('alreadyDownload');
                } else {
                    console.error("Không thể tải ảnh.");
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
            });
        }

        // function effectDownload(elementButton){
        //     /* bật loading */
        //     toggleWaiting();
        //     /* lấy thông tin thẻ a được click */
        //     var link = elementButton.attr('href');
        //     window.location.href = link;
        //     /* thực thi */
        //     var downloadTimer = setInterval(function() {
        //         if (downloadComplete(link)) {
        //             clearInterval(downloadTimer);
        //             /* addclass đã download */
        //             elementButton.addClass('alreadyDownload');
        //             elementButton.find('.wallpaperSourceGrid_item_background').css('display', 'flex');
        //             elementButton.find('.wallpaperSourceGrid_item_action').css('display', 'flex');
        //             elementButton.find('.wallpaperSourceGrid_item_action img').attr('src', '/storage/images/svg/download-success.svg');                    
        //             /* tắt loading */
        //             toggleWaiting();
        //         }
        //     }, 100);
        // }

        // function fixedElement(){
        //     var elementOffset   = $("#js_scrollMenu").offset().top;
        //     var elementWidth    = $("#js_scrollMenu").outerWidth();
        //     $(window).scroll(function() {
        //         var scroll          = $(window).scrollTop();
        //         if (scroll>=elementOffset&&$(window).width()>1199) {
        //             $("#js_scrollMenu").css({
        //                 position: "fixed", 
        //                 top: "calc(60px + 2rem)", 
        //                 width: 'inherit',
        //                 transition: 'all 0.3s ease-in-out'
        //             });
        //         } else {
        //             $("#js_scrollMenu").css({
        //                 position: "relative", 
        //                 top: "0", 
        //                 width: 'inherit', 
        //                 transform: "translateY(0)"
        //             });
        //         }
        //     });
        // }

        // function toggleWaiting(action = 'unset'){
        //     const element   = $('#js_toggleWaiting_box');
        //     const displayE  = element.css('display');
        //     if(displayE=='none'){
        //         /* hiển thị */
        //         element.css('display', 'flex');
        //         $('body').css('overflow', 'hidden');
        //         $('#js_openCloseModal_blur').addClass('blurBackground');
        //     }else {
        //         element.css('display', 'none');
        //         $('body').css('overflow', 'unset');
        //         $('#js_openCloseModal_blur').removeClass('blurBackground');
        //     }
        // }
        function lazyloadSource() {
            $('img.lazyloadSource').each(function() {
                var boxThis = $(this);
                if (!boxThis.hasClass('loaded')) {
                    var distance = $(window).scrollTop() - boxThis.offset().top + 900;
                    if (distance > 0) {
                        loadImageSource(boxThis);
                    }
                }
            });
        }
        function loadImageSource(boxThis) {
            const orderCode     = boxThis.data('order-code');
            const fileName      = boxThis.data('file-name');
            $.ajax({
                url: "{{ route('ajax.loadImageSource') }}",
                type: 'get',
                dataType: 'html',
                data: {
                    order_code  : orderCode,
                    file_name   : fileName
                }
            }).done(function (response) {
                boxThis.attr({
                    'src'   : response,
                    'style' : ''
                });
                boxThis.addClass('loaded');
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
            });
        }
    </script>
@endpush