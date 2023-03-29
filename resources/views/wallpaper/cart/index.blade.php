@extends('layouts.wallpaper')
@push('headCustom')
@section('content')
    <div style="overflow:hidden;">
        <div class="container">
            <div class="breadcrumbMobileBox">
                @include('wallpaper.template.breadcrumb')
            </div>
            <div class="contentBox">
                @php
                    $count          = $products->count();
                    $total          = 0;
                    foreach($productsCart as $product) $total += $product['price'];
                @endphp
                <h1>Danh sách sản phẩm (<span id="js_updateCart_count" class="highLight">{{ $count }}</span>)</h1>
                <div class="pageCartBox">
                    <div class="pageCartBox_left">
                        <!-- danh sách sản phẩm -->
                        <div class="pageCartBox_left_item">
                            <div class="cartSectionBox">
                                {{-- <div class="cartSectionBox_title">
                                    
                                </div> --}}
                                <div class="cartSectionBox_body">
                                    <div class="cartProductBox_head">
                                        <div>Sản phẩm</div>
                                        <div>Đơn giá</div>
                                        {{-- <div>Số lượng</div> --}}
                                        {{-- <div>Thành tiền</div> --}}
                                    </div>
                                    <div class="cartProductBox_body">
                                        @foreach($products as $product)
                                            @php
                                                $keyId  = !empty($product->id)&&!empty($product->price->id) ? $product->id.$product->price->id : null;
                                            @endphp
                                            <div id="{{ 'js_updateCart_idWrite_'.$keyId }}" class="cartProductBox_body_item">
                                                @include('wallpaper.cart.cartRow', compact('product'))
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pageCartBox_right">
                        <div class="cartSectionBox">
                            <div class="cartSectionBox_body">
                                <div class="total">
                                    <div>Tổng đơn hàng:</div>
                                    <div class="total_number"><span id="js_updateCart_total">{!! number_format($total).config('main.currency_unit') !!}</span></div>
                                </div>
                            </div>
                            {{-- <div class="cartSectionBox_notice">
                                Dùng mã giảm giá của {{ config('main.company_name') }} ở bước sau
                            </div> --}}
                            <div class="cartSectionBox_button">
                                {{-- <a href="{{ route('main.checkout') }}" class="button">Thanh toán</a> --}}
                                <a href="#" class="button" onClick="noticeContrustion();">Thanh toán</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection
@push('modal')

@endpush
@push('bottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        
    </script>
@endpush