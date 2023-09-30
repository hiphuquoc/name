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
                                    Thanh toán thành công!
                                </div>
                                <div class="confirmMessageBox_left_desc">
                                    Cảm ơn bạn đã ủng hộ {{ config('main.company_name') }}. Hình ảnh của bạn ở phần bên dưới hoặc bạn có thể truy cập email của mình để tải ảnh.
                                </div>
                            </div>
                            <div class="confirmMessageBox_right">
                                <div class="confirmMessageBox_right_item">
                                    Mã đơn <span class="highLight">{{ $order->code }}</span>
                                </div>
                                @if(!empty($order->email))
                                    <div class="confirmMessageBox_right_item">
                                        Email: {{ $order->email }}
                                    </div>
                                @endif
                                <div class="confirmMessageBox_right_item">
                                    {{ $order->paymentMethod->name }} lúc {{ date('H:i\, d/m/Y', strtotime($order->created_at))}}
                                </div>
                                <div class="confirmMessageBox_right_item">
                                    Tổng thanh toán <span class="price">{{ number_format($order->total) }}{!! config('main.currency_unit') !!}</span>
                                </div>
                                <div class="confirmMessageBox_right_item">
                                    <table class="noResponsive">
                                        <thead>
                                            <tr>
                                                <td>Tải hình ảnh:</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->products as $product)
                                                <tr>
                                                    <td>
                                                        {{-- @php
                                                            $zipPath = $product->infoPrice->folder_drive.'-'.$product->infoProduct->seo->slug.'/'.$product->infoPrice->folder_drive.'-'.$product->infoProduct->seo->slug.'.zip';
                                                        @endphp --}}
                                                        <a href="#" target="_blank">
                                                            <img src="{{ Storage::url('images/svg/download-success.svg') }}" />
                                                            <div>{{ $product->infoProduct->name }} (link google drive .ZIP)</div>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="wallpaperSourceGrid">
                            @foreach($order->products as $product)
                                @if(empty($product->infoPrice))
                                    <!-- trường hợp all -->
                                    @foreach($product->infoProduct->prices as $price)
                                        @foreach($price->wallpapers as $wallpaper)
                                            @php
                                                // $imagePath      = Storage::disk('google')->url($source->file_path);
                                                // if($i<5){
                                                //     $attrImage  = 'class="wallpaperSourceGrid_item_image" style="background:url(\''.$imagePath.'\') no-repeat center center / cover;"';
                                                // }else {
                                                //     $attrImage  = 'class="wallpaperSourceGrid_item_image lazyload" data-src="'.$imagePath.'"';
                                                // }
                                                $attrImage = 'class="wallpaperSourceGrid_item_image" style="background:url(\''.$wallpaper->infoWallpaper->file_url_hosting.'\') no-repeat center center / cover;"';
                                            @endphp
                                            <a href="{{ route('main.downloadSource', ['file' => $wallpaper->infoWallpaper->file_url_hosting]) }}" class="wallpaperSourceGrid_item">
                                                {{-- <div class="wallpaperSourceGrid_item_image"></div> --}}
                                                <div class="wallpaperSourceGrid_item_action">
                                                    <img class="lazyloadSource" src="{{ Storage::url('images/svg/download.svg') }}" data-order-code="{{ $order->code }}" data-wallpaper-id="{{ $wallpaper->infoWallpaper->id }}" />
                                                </div>
                                                <div class="wallpaperSourceGrid_item_background"></div>
                                            </a>
                                        @endforeach
                                    @endforeach
                                @else
                                    @foreach($product->infoPrice->wallpapers as $wallpaper)
                                        <a href="{{ route('main.downloadSource', ['file' => $wallpaper->file_url_hosting]) }}" class="wallpaperSourceGrid_item">
                                            <div {!! $attrImage !!}></div>
                                            <div class="wallpaperSourceGrid_item_action">
                                                <img src="{{ Storage::url('images/svg/download.svg') }}" />
                                            </div>
                                            <div class="wallpaperSourceGrid_item_background"></div>
                                        </a>
                                    @endforeach
                                @endif
                            @endforeach
                            <form id="js_downloadSource_form" method="post" action="{{ route('main.downloadSource') }}">
                                @csrf
                                <input id="js_downloadSource_input" type="hidden" name="folder_path" value="" />
                            </form>
                        </div>

                    </div>
                    {{-- <div class="pageCartBox_right">
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
                            </div>
                        </div>
                    </div> --}}
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
            if($("#js_scrollMenu").length) fixedElement();
            // toggleWaiting();
            lazyloadSource();
        });

        $('a.wallpaperSourceGrid_item').click(function(e) {
            e.preventDefault();
            effectDownload($(this));
        });

        function effectDownload(elementButton){
            /* bật loading */
            toggleWaiting();
            /* lấy thông tin thẻ a được click */
            var link = elementButton.attr('href');
            window.location.href = link;
            /* thực thi */
            var downloadTimer = setInterval(function() {
                if (downloadComplete(link)) {
                    clearInterval(downloadTimer);
                    /* addclass đã download */
                    elementButton.addClass('alreadyDownload');
                    elementButton.find('.wallpaperSourceGrid_item_background').css('display', 'flex');
                    elementButton.find('.wallpaperSourceGrid_item_action').css('display', 'flex');
                    elementButton.find('.wallpaperSourceGrid_item_action img').attr('src', '/storage/images/svg/download-success.svg');                    
                    /* tắt loading */
                    toggleWaiting();
                }
            }, 100);
        }

        function downloadComplete(link) {
            var request = new XMLHttpRequest();
            request.open('HEAD', link, false);
            request.send();
            if (request.readyState === 4 && request.status === 200) {
                return true;
            } else {
                return false;
            }
        }

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

        function toggleWaiting(action = 'unset'){
            const element   = $('#js_toggleWaiting_box');
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

        /* lazyload và resize từ ảnh gốc */
        function lazyloadSource() {
            $('img.lazyloadSource, div.lazyloadSource').each(function() {
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
            const idWallpaper = boxThis.data('wallpaper-id');
            $.ajax({
                url: "{{ route('ajax.loadImageSource') }}",
                type: 'get',
                dataType: 'html',
                data: {
                    order_code          : orderCode,
                    wallpaper_info_id   : idWallpaper
                }
            }).done(function (response) {
                console.log(response);
                // if (boxThis.is('img')) {
                //     boxThis.attr('src', response);
                // } else if (boxThis.is('div')) {
                //     boxThis.css({
                //         background: 'url("' + response + '") no-repeat center center / cover',
                //         filter: 'unset'
                //     });
                // }
                // boxThis.addClass('loaded');
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
            });
        }
    </script>
@endpush