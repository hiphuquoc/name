@extends('layouts.wallpaper')
@push('headCustom')
    <!-- Event snippet for Lượt xem trang conversion page -->
    @php
        $tmp = \App\Helpers\Number::getPriceByLanguage($order->total, 'vi');
        $priceConfirm = $tmp['number'];
    @endphp
    <script>
        gtag('event', 'conversion', {
            'send_to': 'AW-16558810206/GKHLCKSGg64ZEN7I7dc9',
            'value': '{{ $priceConfirm }}',
            'currency': 'VND'
        });
    </script>
@endpush
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
                                {{-- <div class="confirmMessageBox_right_item">
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
                                </div> --}}
                            </div>
                        </div>

                        <div class="wallpaperSourceGrid">
                            @foreach($order->products as $product)
                                {{-- @php
                                    dd($product->toArray());
                                @endphp --}}
                                @if(!empty($product->infoPrice))
                                    @foreach($product->infoPrice->wallpapers as $wallpaper)
                                        <a href="{{ route('ajax.downloadImgFreeWallpaper', ['file_cloud' => $wallpaper->infoWallpaper->file_cloud_source]) }}" class="wallpaperSourceGrid_item" download>
                                            <div class="wallpaperSourceGrid_item_image">
                                                <img class="lazyload" src="{{ \App\Helpers\Image::getUrlImageCloud($wallpaper->infoWallpaper->file_cloud_source) }}" />
                                            </div>
                                            <div class="wallpaperSourceGrid_item_action">
                                                <img src="{{ Storage::url('images/svg/download.svg') }}" />
                                            </div>
                                            <div class="wallpaperSourceGrid_item_background"></div>
                                        </a>
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