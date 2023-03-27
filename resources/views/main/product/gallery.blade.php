<div class="container">
    <!-- Product Detail Box -->
    <div class="pageProductDetailBox">
        <div class="pageProductDetailBox_left">
            @php
                $flagHasImage = false;
                foreach($item->prices as $price){
                    foreach($price->files as $file){
                        if(file_exists(Storage::path($file->file_path))) {
                            $flagHasImage = true;
                            break;
                        }
                    }
                }
            @endphp
            @if($flagHasImage)
                <!-- Gallery Desktop -->
                <div class="sectionProductBox hide-990">
                    <div class="galleryProductBox">
                        @foreach($item->prices as $price)
                            @foreach($price->files as $file)
                                <div class="galleryProductBox_item">
                                    <img src="{{ Storage::url($file->file_path) }}" alt="{{ $item->name ?? $item->seo->title ?? null }}" title="{{ $item->name ?? $item->seo->title ?? null }}" data-option="js_addToCart_option_{{ $price->id }}" />
                                    <div class="galleryProductBox_item_note">Ảnh của <span class="highLight">{{ $price->name }}</span></div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

            @endif
            <!-- Content -->
            @include('main.product.content', ['contents' => $item->contents])
        </div>
        <div class="pageProductDetailBox_right">
            <div class="productDetailBox">
                <div class="productDetailBox_detail">
                    <!-- id hidden -->
                    <input type="hidden" id="product_info_id" name="product_info_id" value="{{ $item->id ?? null }}" />
                    <!-- tiêu đề -->
                    <h1 class="productDetailBox_detail_title">{{ $item->name ?? $item->seo->title ?? null }}</h1>
                    <!-- đánh giá -->
                    <div class="productDetailBox_detail_rating">
                        <div class="ratingBox">
                            @if(!empty($item->sold))
                                <div class="ratingBox_numberSell">
                                    Đã bán <span>{{ $item->sold }}</span>
                                </div>
                            @endif
                            @if(!empty($item->seo->rating_aggregate_star)&&!empty($item->seo->rating_aggregate_count))
                                <div class="ratingBox_star">
                                    <div class="ratingBox_star_box">
                                        <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                                        <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                                        <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                                        <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                                        <span class="ratingBox_star_box_on"><i class="fas fa-star-half-alt"></i></span>
                                    </div>
                                    <div class="ratingBox_star_total">
                                        <span>{{ $item->seo->rating_aggregate_star }}</span> sao/<span>{{ $item->seo->rating_aggregate_count }}</span> đánh giá
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- mô tả sản phẩm -->
                    @if(!empty($item->description))
                        <div id="js_viewMoreContent_content" class="productDetailBox_detail_description">
                            {!! $item->description !!}
                        </div>
                        {{-- <div id="js_checkViewMore_button" class="viewMoreText" onClick="viewMoreContent(this, 'js_viewMoreContent_content', 'maxLine_5')" style="display:none;">
                            Xem thêm 
                        </div> --}}
                    @endif
                    <!-- Gallery Mobile -->
                    <div id="galleryMobile" class="show-990">
                        <div class="galleryProductBox mobile">
                            @foreach($item->prices as $price)
                                @foreach($price->files as $file)
                                    <div class="galleryProductBox_item">
                                        <img src="{{ Storage::url($file->file_path) }}" alt="{{ $item->name ?? $item->seo->title ?? null }}" title="{{ $item->name ?? $item->seo->title ?? null }}" data-option="js_addToCart_option_{{ $price->id }}" />
                                        <div class="galleryProductBox_item_note">Ảnh của <span class="highLight">{{ $price->name }}</span></div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <!-- option -->
                    <div class="productDetailBox_detail_option">
                        <div class="productDetailBox_detail_option_title">
                            Tùy chọn 
                        </div>
                        <div id="js_addToCart_options" class="productDetailBox_detail_option_box">
                            @foreach($item->prices as $option)
                                @php
                                    $selected = null;
                                    if($loop->index==0) $selected = 'selected';
                                @endphp
                                <div id="js_addToCart_option_{{ $option->id }}" class="productDetailBox_detail_option_box_item {{ $selected }}"
                                    data-product_price_id="{{ $option->id }}" 
                                    data-option_name="{{ $option->name }}" 
                                    data-price="{{ $option->price }}"
                                    onClick="setOptionProduct('{{ $option->id }}');">
                                    {{ $option->name }}
                                </div>  
                            @endforeach
                        </div>
                    </div>
                    <!-- giá -->
                    <div class="productDetailBox_detail_price">
                        @include('main.product.priceBox', ['prices' => $item->prices])
                    </div>
                    <!-- button đặt hàng -->
                    <div class="productDetailBox_detail_checkout">
                        <div class="productDetailBox_detail_checkout_qty">
                            <div class="inputQty">
                                <div class="inputQty_button minus" onClick="plusMinusQuantity('js_addToCart_quantity', 'minus');"><i class="fa-solid fa-minus"></i></div>
                                <input id="js_addToCart_quantity" type="number" name="quantity" value="1" />
                                <div class="inputQty_button plus" onClick="plusMinusQuantity('js_addToCart_quantity', 'plus');"><i class="fa-solid fa-plus"></i></div>       
                            </div>                 
                        </div>
                        <div class="productDetailBox_detail_checkout_button">
                            <button type="button" class="button maxLine_1" onClick="addToCart();" aria-label="Thêm vào giỏ hàng">Thêm vào giỏ hàng</button>
                        </div>
                    </div>
                    <!-- ==== -->
                    @include('main.product.descShip')
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Related -->
    @if(!empty($related)&&$related->isNotEmpty())
        <div class="relatedProductBox">
            <div class="relatedProductBox_title">
                <h2>Gợi ý cho bạn</h2>
            </div>
            <div class="relatedProductBox_box">

                @include('main.template.productGrid', ['products' => $related])

            </div>
        </div>
    @endif
</div>
@push('scriptCustom')
    <script type="text/javascript">
        $(window).ready(function(){
            checkViewMore('js_viewMoreContent_content');
        })

        $('#galleryMobile .galleryProductBox').slick({
            dots: false,
            arrows: false,
            infinite: false,
            speed: 300,
            slidesToShow: 2.6,
            slidesToScroll: 2,
            responsive: [
                {
                    breakpoint: 766,
                    settings: {
                        slidesToShow: 1.6,
                        slidesToScroll: 1
                    }
                },
            ]
        });
        function checkViewMore(idContent, limit = 350){
            const element   = $('#'+idContent);
            if(element.outerHeight()>limit){
                element.addClass('customScrollBar-y');
                element.css('height', limit+'px');
                // $('#js_checkViewMore_button').css('display', 'block');
            }
        }
        // function viewMoreContent(elementButton, idContent, className){
        //     const element = $('#'+idContent);
        //     if(element.hasClass(className)){
        //         /* đang dóng */
        //         element.removeClass(className);
        //         $(elementButton).html('Ẩn bớt');
        //         $(elementButton).addClass('open');
        //     }else {
        //         /* đang mở */
        //         element.addClass(className);
        //         $(elementButton).html('Xem thêm');
        //         $(elementButton).removeClass('open');
        //     }
        // }
    </script>
@endpush