@extends('layouts.checkout')
@section('title')
    Xác nhận đơn hàng {{ $order->code }} thành công
@endsection
@section('content')
<div id="js_printContent">
<div class="pageCheckout" style="min-height:unset;margin-bottom:5.5rem;">
    <div class="pageCheckout_content" style="max-width:660px;margin-right:0;">
        <div class="pageCheckout_content_box">
            
            <div class="pageCheckout_content_box_logo">
                {{-- <div class="logoMain"></div> --}}
                <img src="{{ Storage::url(config('main.logo_main')) }}" />
            </div>
            <div class="pageCheckout_content_box_body">
                <!-- lời cảm ơn -->
                {{-- <div class="messageOrderSuccess notPrint">
                    <div class="messageOrderSuccess_image">
                        <img src="{{ Storage::url('images/icon-order-succes.png') }}" />
                    </div>
                    <div class="messageOrderSuccess_text">Cảm ơn bạn đã đặt hàng!</div>
                </div> --}}
                <!-- danh sách sản phẩm -->
                <div class="confirmSectionBox">
                    <div class="confirmSectionBox_title" style="margin-bottom:1.25rem;">
                        Đơn hàng: #{{ $order->code }} - đặt lúc {{ date('H:i, d/m/Y', strtotime($order->created_at))}}
                    </div>
                    <div class="confirmSectionBox_body">

                        <div class="checkoutProductBox_body">
                            @foreach($order->products as $product)
                                @php
                                    $title      = $product->infoProduct->name ?? $product->infoProduct->seo->title ?? null;
                                    /* ảnh */
                                    $image      = config('image.default_square');
                                    if(!empty($product->infoPrice->files[0]->file_path)&&file_exists(Storage::path($product->infoPrice->files[0]->file_path))) $image = Storage::url($product->infoPrice->files[0]->file_path);
                                @endphp
                                <div class="checkoutProductBox_body_item">
                                    <div class="checkoutProductBox_body_item_image">
                                        <img src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
                                        <div class="checkoutProductBox_body_item_image_quantity">{{ $product->quantity }}</div>
                                    </div>
                                    <div class="checkoutProductBox_body_item_content">
                                        <div class="checkoutProductBox_body_item_content_title maxLine_2">
                                            {{ $title }}
                                        </div>
                                        <div class="checkoutProductBox_body_item_content_option">
                                            {{ $product->infoPrice->name ?? null }}
                                        </div>
                                    </div>  
                                    <div class="checkoutProductBox_body_item_price">
                                        {!! number_format($product->price*$product->quantity).config('main.currency_unit') !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                {{-- @php
                    dd($order->toArray());
                @endphp --}}

                <!-- chi tiết thanh toán -->
                <div class="detailPayment" style="margin-top:1rem;">
                    <div class="detailPayment_item">
                        <div>Phí vận chuyển:</div> 
                        <div>{!! !empty($order->ship_cash) ? number_format($order->ship_cash).config('main.currency_unit') : 0 !!}</div>
                    </div>
                    <div class="detailPayment_item">
                        <div>Tổng cộng:</div> 
                        <div class="priceTotal">{!! number_format($order->total).config('main.currency_unit') !!}</div>
                    </div>
                    <div class="detailPayment_item">
                        <div>Hình thức thanh toán</div> 
                        <div>{{ $order->paymentMethod->name ?? 'Không xác định' }}</div>
                    </div>
                </div>

                @php
                    // dd($order);
                @endphp

                <!-- thông tin giao hàng -->
                <div class="confirmSectionBox">
                    <div class="confirmSectionBox_title">
                        Thông tin giao hàng
                    </div>
                    <div class="confirmSectionBox_body">
                        <div class="confirmSectionBox_body_item">
                            <div>Người nhận:</div>
                            <div>{{ $order->customer->name }}</div>
                        </div>
                        <div class="confirmSectionBox_body_item">
                            <div>Số điện thoại:</div>
                            <div>{{ $order->customer->phone }}</div>
                        </div>
                        <div class="confirmSectionBox_body_item">
                            <div>Địa chỉ:</div>
                            @php
                                $fullAddress = [];
                                if(!empty($order->address)) $fullAddress[] = $order->address;
                                if(!empty($order->district->district_name)) $fullAddress[] = $order->district->district_name;
                                if(!empty($order->province->province_name)) $fullAddress[] = $order->province->province_name;
                                $fullAddress = implode(', ', $fullAddress);
                            @endphp
                            <div>{{ $fullAddress }}</div>
                        </div>
                    </div>
                </div>
                <!-- vận chuyển -->
                @if(!empty($order->ship_note))
                    <div class="confirmSectionBox">
                        <div class="confirmSectionBox_title">
                            Phương thức vận chuyển
                        </div>
                        <div class="confirmSectionBox_body">
                            <div>{{ $order->ship_note }}</div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="pageCheckout_content_box_footer">
                Quý khách sẽ nhận được hàng trong khoảng thời gian từ 2 - 3 ngày kể từ ngày xác nhận đơn hàng này.<br/>
                {{ config('main.company_name') }} xin cảm ơn!
            </div>
        </div>
    </div>
</div>
</div>

@if(!empty($action)&&$action==true)
    <div class="buttonBoxPageConfirm">
        <div class="buttonBoxPageConfirm_box">
            <a href="/"><i class="fa-solid fa-arrow-left"></i>Tiếp tục mua sắm</a> 
            <div class="button" onClick="printContent('js_printContent');"><i class="fa-solid fa-print"></i>In đơn hàng</div>
        </div>
    </div>
@endif

<script type="text/javascript">
    function printContent(el){
        var restorepage = document.body.innerHTML;
        var printableArea = document.getElementById(el);
        var notToPrints = document.getElementsByClassName("notPrint");
        while (notToPrints.length > 0) {
            notToPrints[0].parentNode.removeChild(notToPrints[0]);
        }
        document.body.innerHTML = printableArea.innerHTML;
        window.print();
        document.body.innerHTML = restorepage;
    }
</script>
@endsection