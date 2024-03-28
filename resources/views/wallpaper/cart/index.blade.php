@extends('layouts.wallpaper')
@push('headCustom')
@section('content')
    <div style="overflow:hidden;">
        <div class="container">
            <div class="breadcrumbMobileBox">
                @include('wallpaper.template.breadcrumb')
            </div>
            <div class="contentBox">
                <form id="formPaymentMethod" action="{{ route('main.paymentCart') }}" method="post" style="width:100%;">
                    @csrf
                    <h1>{{ config('language.'.$language.'.data.product_list') }} (<span id="js_updateCart_count" class="highLight">{{ $detailCart['count'] ?? 0 }}</span>)</h1>
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
                                                <div>{{ config('language.'.$language.'.data.product') }}</div>
                                                <div>{{ config('language.'.$language.'.data.unit_price') }}</div>
                                            </div>
                                            <div class="cartProductBox_body">
                                                @foreach($products as $product)
                                                    <!-- trường hợp trọn bộ -->
                                                    @if(count($product->cart['product_price_id'])>=$product->prices->count())
                                                        @php
                                                            $arrayProductPrice  = $product->cart['product_price_id'];
                                                            $keyId              = !empty($product->id) ? $product->id.implode('-', $product->cart['product_price_id']) : null;
                                                        @endphp
                                                        <div id="{{ 'js_updateCart_idWrite_'.$keyId }}" class="cartProductBox_body_item">
                                                            @include('wallpaper.cart.cartRow', compact('product', 'arrayProductPrice','language'))
                                                        </div>
                                                    @else 
                                                        @foreach($product->cart['product_price_id'] as $p)
                                                            @php
                                                                $arrayProductPrice  = [$p];
                                                                $keyId              = !empty($product->id) ? $product->id.$p : null;
                                                            @endphp
                                                            <div id="{{ 'js_updateCart_idWrite_'.$keyId }}" class="cartProductBox_body_item">
                                                                @include('wallpaper.cart.cartRow', compact('product', 'arrayProductPrice', 'language'))
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- form email và phương thức thanh toán -->
                                @include('wallpaper.cart.formPayment', [
                                    'language'  => $language,
                                    'action'    => 'chooseOptionPayment'
                                ])
                            @else 
                                @include('wallpaper.cart.emptyCart', compact('language'))
                            @endif

                        </div>
                        <div class="pageCartBox_right">
                            @if(!empty($products)&&$products->isNotEmpty())
                            <div id="js_scrollMenu" class="cartSectionBox">
                                <div id="js_loadTotalCart" class="cartSectionBox_body">
                                    @include('wallpaper.cart.total', [
                                        'intoMoney'     => $detailCart['into_money'],
                                        'total'         => $detailCart['total'],
                                        'language'      => $language,
                                        'taxNumber'     => 0
                                    ])
                                </div>
                                {{-- <div class="cartSectionBox_notice">
                                    Dùng mã giảm giá của {{ config('main.company_name') }} ở bước sau
                                </div> --}}
                                <div class="cartSectionBox_button">
                                    <div class="button" onClick="submitFormPayment('formPaymentMethod');">{{ config('language.'.$language.'.data.payment') }}</div>
                                </div>
                            </div>
                            @endif
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
                    $('#'+idForm).submit();
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