<div class="emptyCartBox">
    <img src="{{ Storage::url('images/svg/icon-blank-cart.svg') }}" alt="{{ config('language.'.$language.'.data.your_cart_is_empty') }}" title="{{ config('language.'.$language.'.data.your_cart_is_empty') }}" />
    <div class="emptyCart_text">{{ config('language.'.$language.'.data.your_cart_is_empty') }}</div> 
    <a href="/{{ config('language.'.$language.'.key') }}" class="emptyCartBox_button button" aria-label="{{ config('language.'.$language.'.data.continue_shopping') }}">{{ config('language.'.$language.'.data.continue_shopping') }}</a>
</div>