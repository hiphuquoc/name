@extends('layouts.main')
@push('headCustom')
@section('content')
    {{-- <div style="overflow:hidden;"> --}}
        <!-- === START:: Header Top === -->
        @include('main.snippets.headerTop')
        <!-- === END:: Header Top === -->
        <!-- === START:: Header Main === -->
        @include('main.snippets.headerMain')
        <!-- === END:: Header Main === -->
        <div class="background_2">
            @include('main.template.breadcrumb', compact('breadcrumb'))

            <div class="contentBox">
                <div class="container">
                    <h1>Giỏ hàng</h1>
                    <div class="pageCartBox">
                        <div class="pageCartBox_left">
                            <!-- danh sách sản phẩm -->
                            <div class="pageCartBox_left_item">
                                <div class="cartSectionBox">
                                    <div class="cartSectionBox_title">
                                        @php
                                            $count          = 0;
                                            $total          = 0;
                                            foreach($productsCart as $product){
                                                $count      += $product['quantity'];
                                                $total      += $product['quantity']*$product['price'];
                                            }
                                        @endphp
                                        Danh sách sản phẩm (<span id="js_updateCart_count" class="highLight">{{ $count }}</span>)
                                    </div>
                                    <div class="cartSectionBox_body">
                                        <div class="cartProductBox_head">
                                            <div>Sản phẩm</div>
                                            <div>Đơn giá</div>
                                            <div>Số lượng</div>
                                            <div>Thành tiền</div>
                                        </div>
                                        <div class="cartProductBox_body">
                                            @foreach($products as $product)
                                                @php
                                                    $keyId  = !empty($product->id)&&!empty($product->price->id) ? $product->id.$product->price->id : null;
                                                @endphp
                                                <div id="{{ 'js_updateCart_idWrite_'.$keyId }}" class="cartProductBox_body_item">
                                                    @include('main.cart.cartRow', compact('product'))
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
                                        <div>Tổng đơn hàng:<div style="font-weight:normal;">(tạm tính)</div></div>
                                        <div class="total_number"><span id="js_updateCart_total">{!! number_format($total).config('main.currency_unit') !!}</span></div>
                                    </div>
                                </div>
                                <div class="cartSectionBox_notice">
                                    Dùng mã giảm giá của {{ config('main.company_name') }} ở bước sau
                                </div>
                                <div class="cartSectionBox_button">
                                    <a href="{{ route('main.checkout') }}" class="button">Đặt hàng</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{-- </div> --}}
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