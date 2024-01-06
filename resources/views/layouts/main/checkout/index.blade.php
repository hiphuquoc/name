@extends('layouts.checkout')
@section('content')
@php
    $count          = 0;
    $total          = 0;
    foreach($productsCart as $product){
        $count      += $product['quantity'];
        $total      += $product['quantity']*$product['price'];
    }
@endphp
<form id="formCheckout" action="{{ route('main.order') }}" method="POST">
@csrf
<div class="pageCheckout">
<div class="pageCheckout_content">
    <div class="pageCheckout_content_box">
        
        <div class="pageCheckout_content_box_logo">
            <div class="logoMain"></div>
        </div>
        <div class="pageCheckout_content_box_body">
            <div class="column">
                <div class="column_item">
                    <div class="checkoutSectionBox">
                        <div class="checkoutSectionBox_title">
                            Thông tin giao hàng
                        </div>
                        <div class="checkoutSectionBox_body">
                            <div class="formBox">
                                <div class="formBox_item inputWithLabelInside">
                                    <label class="inputRequired" for="name">Người nhận</label>
                                    <input type="text" id="name" name="name" value="" onkeyup="validateWhenType(this)" required />
                                </div>
                                <div class="formBox_item inputWithLabelInside">
                                        <label class="inputRequired" for="phone">Số điện thoại</label>
                                        <input type="text" id="phone" name="phone" value="" onkeyup="validateWhenType(this, 'phone')" required />
                                </div>
                                <div class="formBox_item inputWithLabelInside">
                                        <label class="inputRequired" for="address">Địa chỉ</label>
                                        <input type="text" id="address" name="address" value="" onkeyup="validateWhenType(this)" required />
                                </div>
                                <div class="formBox_item inputWithLabelInside">
                                    <label class="inputRequired" for="province_info_id">Tỉnh thành</label>
                                    <select id="province_info_id" name="province_info_id" class="select2" onChange="validateWhenType(this);loadDistrictByIdProvince(this, 'district_info_id');" required>
                                        <option value="0" selected>- Vui lòng chọn -</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->id }}">{{ $province->province_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="formBox_item inputWithLabelInside">
                                    <label class="inputRequired" for="district_info_id">Quận huyện</label>
                                    <select id="district_info_id" name="district_info_id" class="select2" onChange="validateWhenType(this)" required>
                                        <option value="0" selected>- Vui lòng chọn -</option>
                                        <option value="1">Giá trị 1</option>
                                    </select>
                                </div>
                                <div class="formBox_item textareaWithLabelInside">
                                    <label for="note">Ghi chú đơn hàng</label>
                                    <textarea id="note" name="note" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- chi phí ship -->
                <div class="column_item">
                    <div class="checkoutSectionBox">
                        <div class="checkoutSectionBox_title">
                            Vận chuyển
                        </div>
                        <div class="checkoutSectionBox_body">
                            <div class="shipCash">
                                @if($total>498000)
                                    @php
                                        $shipCash = 0;
                                    @endphp
                                    <label class="shipCash_item" for="ship_cash">
                                        <div class="shipCash_item_radio">
                                            <input type="radio" id="ship_cash" name="ship_cash" value="1" checked />
                                        </div>
                                        <div class="shipCash_item_text">Miễn phí đối với đơn hàng trên 498k</div>
                                        <div class="shipCash_item_note">
                                            Miễn phí
                                        </div>
                                    </label>
                                @else 
                                    @php
                                        $shipCash = 20000;
                                    @endphp
                                    <label class="shipCash_item" for="ship_cash">
                                        <div class="shipCash_item_radio">
                                            <input type="radio" id="ship_cash" name="ship_cash" value="1" checked />
                                        </div>
                                        <div class="shipCash_item_text">Phí ship COD toàn quốc 20k</div>
                                        <div class="shipCash_item_note">
                                            {!! number_format($shipCash).config('main.currency_unit') !!}
                                        </div>
                                    </label>
                                @endif
                                <input type="hidden" name="ship_cash" value="{{ $shipCash }}" />
                                <input type="hidden" name="ship_note" value="Miễn phí đối với đơn hàng trên 498k" />
                            </div>
                        </div>
                    </div>
                    <!-- phương thức thanh toán -->
                    <div class="checkoutSectionBox">
                        <div class="checkoutSectionBox_title">
                            Phương thức thanh toán
                        </div>
                        <div class="checkoutSectionBox_body">
                            <div class="paymentList">
                                @foreach($payments as $payment)
                                <label class="paymentList_item" for="payment_method_info_id">
                                    <div class="paymentList_item_radio">
                                        <input type="radio" id="payment_method_info_id" name="payment_method_info_id" value="{{ $payment->id ?? 0 }}" {{ $loop->index==0 ? 'checked' : null }} />
                                    </div>
                                    <div class="paymentList_item_text">{{ $payment->name ?? null }}</div>
                                    <div class="paymentList_item_icon">
                                        <img src="{{ Storage::url($payment->icon) }}" />
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pageCheckout_content_box_footer">
            Sau khi hoàn tất đơn hàng khoảng 60-90 phút (trong giờ hành chính), {{ config('main.company_name') }} sẽ nhanh chóng gọi điện xác nhận lại thời gian giao hàng với bạn. {{ config('main.company_name') }} xin cảm ơn!
        </div>

    </div>
</div> 
<div class="pageCheckout_sidebar">
    <div class="pageCheckout_sidebar_box">
        <!-- danh sách sản phẩm -->
        <div class="checkoutProductBox">
            <div class="checkoutProductBox_title">
                Đơn hàng ({{ $count }} sản phẩm)
            </div>
            <div class="checkoutProductBox_body">
                @foreach($products as $product)
                    @php
                        $title      = $product->name ?? $product->seo->title ?? null;
                        /* ảnh */
                        $image      = config('image.default');
                        if(!empty($product->price->files[0]->file_path)&&file_exists(Storage::path($product->price->files[0]->file_path))) $image = Storage::url($product->price->files[0]->file_path);
                    @endphp
                    <div class="checkoutProductBox_body_item">
                        <div class="checkoutProductBox_body_item_image">
                            <img src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
                            <div class="checkoutProductBox_body_item_image_quantity">{{ $product->cart['quantity'] }}</div>
                        </div>
                        <div class="checkoutProductBox_body_item_content">
                            <div class="checkoutProductBox_body_item_content_title maxLine_2">
                                {{ $title }}
                            </div>
                            <div class="checkoutProductBox_body_item_content_option">
                                {{ $product->price->name ?? null }}
                            </div>
                        </div>  
                        <div class="checkoutProductBox_body_item_price">
                            {!! number_format($product->price->price*$product->cart['quantity']).config('main.currency_unit') !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- mã giảm giá -->
        <div class="promotionCodeBox">
            <div class="promotionCodeBox_body">
                <div class="promotionCodeBox_body_input">
                    <label>Mã giảm giá</label>
                    <input type="text" name="promotion_code" value="" />
                </div>
                <div class="promotionCodeBox_body_button">
                    <div class="button">Áp dụng</div>
                </div>
            </div>
        </div>
        <!-- chi tiết thanh toán -->
        <div class="detailPayment">
            <div class="detailPayment_item">
                <div>Tạm tính:</div> 
                <div>{!! number_format($total).config('main.currency_unit') !!}</div>
            </div>
            <div class="detailPayment_item">
                <div>Phí vận chuyển:</div> 
                <div>{!! number_format($shipCash).config('main.currency_unit') !!}</div>
            </div>
            <div class="detailPayment_item">
                <div>Tổng cộng:</div> 
                <div class="priceTotal">{!! number_format($total+$shipCash).config('main.currency_unit') !!}</div>
            </div>
            <div class="detailPayment_item buttonBox">
                <a href="/gio-hang"><i class="fa-solid fa-arrow-left"></i>Quay lại giỏ hàng</a> 
                <div class="button" onClick="submitForm('formCheckout');">Đặt hàng</div>
            </div>
        </div>
    </div>
</div>
</div>
</form>
<!-- Script -->
<script src="{{ asset('sources/admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/scripts/forms/form-select2.min.js') }}"></script>
<script type="text/javascript">
    function submitForm(idForm){
        event.preventDefault();
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
        }
    }
    /* validate form khi nhập */
    function validateWhenType(elementInput, type = 'empty'){
        const idElement         = $(elementInput).attr('id');
        const parent            = $(document).find('[for*="'+idElement+'"]').parent();
        /* validate empty */
        if(type=='empty'){
            const valueElement  = $.trim($(elementInput).val());
            if(valueElement!=''&&valueElement!='0'){
                parent.removeClass('validateErrorEmpty');
                parent.addClass('validateSuccess');
            }else {
                parent.removeClass('validateSuccess');
                parent.addClass('validateErrorEmpty');
            }
        }
        /* validate phone */ 
        if(type=='phone'){
            const valueElement = $.trim($(elementInput).val());
            if(valueElement.length>=10&&/^\d+$/.test(valueElement)){
                parent.removeClass('validateErrorPhone');
                parent.addClass('validateSuccess');
            }else {
                parent.removeClass('validateSuccess');
                parent.addClass('validateErrorPhone');
            }
        }
        
    }
    /* load quận/huyện */
    function loadDistrictByIdProvince(elementProvince, idWrite){
        const valueProvince = $(elementProvince).val();
        $.ajax({
            url         : '{{ route("ajax.loadDistrictByIdProvince") }}',
            type        : 'get',
            dataType    : 'html',
            data        : {
                province_info_id : valueProvince
            },
            success     : function(response){
                $('#'+idWrite).html(response);
            }
        });
    }
    function validateForm(idForm){
        let error       = [];
        /* input required không được bỏ trống */
        $('#'+idForm).find('input[required]').each(function(){
            /* đưa vào mảng */
            if($(this).val()==''){
                error.push($(this).attr('name'));
            }
        })
        /* select */
        $('#'+idForm).find('select[required]').each(function(){
            if($(this).val()==0) error.push($(this).attr('name'));
        })
        return error;
    }
</script>

@endsection