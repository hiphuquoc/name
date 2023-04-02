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
                <form id="formPaymentMethod" action="{{ route('main.paymentCart') }}" method="post" style="width:100%;">
                    @csrf
                    <h1>Danh sách sản phẩm (<span id="js_updateCart_count" class="highLight">{{ $count }}</span>)</h1>
                    <div class="pageCartBox">
                        <div id="js_checkEmptyCart_idWrite" class="pageCartBox_left">

                            @if(!empty($products)&&$products->isNotEmpty())
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
                                <!-- form email và phương thức thanh toán -->
                                @include('wallpaper.cart.formPayment')
                            @else 
                                @include('wallpaper.cart.emptyCart')
                            @endif

                        </div>
                        <div class="pageCartBox_right">
                            <div id="js_scrollMenu" class="cartSectionBox">
                                <div class="cartSectionBox_body">
                                    <div class="total">
                                        <div>Tổng cộng:</div>
                                        <div class="total_number"><span id="js_updateCart_total">{!! number_format($total).config('main.currency_unit') !!}</span></div>
                                    </div>
                                </div>
                                {{-- <div class="cartSectionBox_notice">
                                    Dùng mã giảm giá của {{ config('main.company_name') }} ở bước sau
                                </div> --}}
                                <div class="cartSectionBox_button">
                                    {{-- <a href="{{ route('main.checkout') }}" class="button">Thanh toán</a> --}}
                                    <div class="button" onClick="submitFormPayment('formPaymentMethod');">Thanh toán</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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

        $(window).ready(function(){
            fixedElement();
        })

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

        let clicked = false;
        function submitFormPayment(idForm){
            event.preventDefault();
            if (!clicked) {
                clicked = true;
                
                const error     = validateForm(idForm);
                if(error==''){
                    // $('#'+idForm).submit(); 
                    noticeContrustion();
                }else {
                    /* thêm class thông báo lỗi cho label của input */
                    for(let i = 0;i<error.length;++i){
                        const idInput = $('#'+idForm).find('[name='+error[i]+']').attr('id');
                        if(idInput!=''){
                            const elementLabel = $('#'+idForm).find('[for='+idInput+']');
                            elementLabel.addClass('error');
                        }
                    }
                    /* scroll đến thông báo lỗi đầu tiên */
                    $('[class*=error]').each(function(){
                        $('html, body').animate({
                            scrollTop: $(this).offset().top - 90
                        }, 300);
                        // $(window).scrollTop(parseInt($(this).offset().top - 90));
                        return false;
                    });
                }

                setTimeout(() => {
                    clicked = false;
                }, 1500);
            }
        }

    </script>
@endpush