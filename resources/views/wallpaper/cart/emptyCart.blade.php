<div class="emptyCartBox">
    <img src="{{ Storage::url('images/svg/icon-blank-cart.svg') }}" alt="{{ config('data_language_1.'.$language.'.your_cart_is_empty') }}" title="{{ config('data_language_1.'.$language.'.your_cart_is_empty') }}" />
    <div class="emptyCart_text">{{ config('data_language_1.'.$language.'.your_cart_is_empty') }}</div> 
    <a href="/{{ config('language.'.$language.'.key') }}" class="emptyCartBox_button button" aria-label="{{ config('data_language_1.'.$language.'.continue_shopping') }}">{{ config('data_language_1.'.$language.'.continue_shopping') }}</a>
</div>