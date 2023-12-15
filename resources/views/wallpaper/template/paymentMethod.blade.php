@php
    $paymentMethods     = \App\Models\PaymentMethod::select('*')
                            ->where('code', '!=', 'cod')
                            ->get();
@endphp
@foreach($paymentMethods as $method)
    @php
        $nameMethodPayment          = empty($language)||$language=='vi' ? $method->name : $method->en_name;
        $descriptionMethodPayment   = empty($language)||$language=='vi' ? $method->description : $method->en_description;
    @endphp
    <div class="paymentMethodBox_item" onclick="chooseOptionPayment(this, {{ $method->id }});">
        <div class="paymentMethodBox_item_logo">
            <img src="{{ Storage::url($method->icon) }}" alt="{{ $nameMethodPayment ?? null }}" title="{{ $nameMethodPayment ?? null }}" />
        </div>
        <div class="paymentMethodBox_item_content">
            <div class="paymentMethodBox_item_content_title">{{ $nameMethodPayment ?? null }}</div>
            <div class="paymentMethodBox_item_content_desc maxLine_1">{{ $descriptionMethodPayment ?? null }}</div>
            @if(!empty($method->fee))
                <div class="paymentMethodBox_item_content_fee">
                    @if(empty($language)||$language=='vi')
                        Phí +
                    @else 
                        Fee +
                    @endif
                    {!! \App\Helpers\Number::getFormatPriceByLanguage($method->fee, $language) !!}
                </div>
            @endif
        </div>
    </div>
@endforeach