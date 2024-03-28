@php
    $productSeo             = [];
    foreach($product->seos as $s){
        if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language) {
            $productSeo     = $s->infoSeo;
            break;
        }
    }
    $productName            = $productSeo->title ?? null;
    /* data filter => data-filter này phải gộp theo thứ tự menu filter từ trên xuống của giá trị (để xây dựng partern filter chính xác) 
        => filter theo danh mục
        => filter theo nhãn hàng
        => filter theo giá
    */
    $dataFilter             = null;
    // $dataFilter         = 'tat-ca-danh-muc tat-ca-nhan-hang';
    // $i                  = 0;
    // foreach($product->categories as $category){
    //     if(!empty($category->infoCategory)) $dataFilter     .= ' '.$category->infoCategory->seo->slug;
    //     ++$i;
    // }
    // /* gộp thêm của brand vào */
    // $dataFilter         .= ' '.$product->brand->seo->slug;
@endphp 
<div class="wallpaperGridBox_item" data-key="{{ $dataFilter }}" data-price="{{ $product->price }}">
    @php
        $i = 0;
    @endphp
    @foreach($product->prices as $price)
        @foreach($price->wallpapers as $wallpaper)
            @if(!empty($wallpaper->infoWallpaper))
                @php
                    $tag        = $tagBox ?? null;
                    $keyIdPrice = 'js_changeOption_'.$tag.$price->id.$wallpaper->infoWallpaper->id;
                    /* lấy ảnh Small */
                    $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
                    $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
                    /* đường dẫn */
                    $url        = $productSeo->slug_full ?? null;
                @endphp
                <div id="{{ $keyIdPrice }}" class="{{ $i==0 ? 'show' : 'hide' }}">
                    <a href="/{{ $url }}?product_price_id={{ $price->id }}" class="wallpaperGridBox_item_image">
                        <div class="zIndexHide">
                            <!-- xử lý loadajax -->
                            @if(!empty($lazyload)&&$lazyload==true)
                                @if($i==0)
                                    <img class="lazyload" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="{{ $productName }}" title="{{ $productName }}" style="filter:blur(8px);" />
                                @else 
                                    <!-- các ảnh sau khi nào click mới load -->
                                    <img class="lazyloadAfter" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="{{ $productName }}" title="{{ $productName }}" style="filter:blur(8px);" />
                                @endif
                            @else 
                                @if($i==0)
                                    <div class="wallpaperGridBox_item_image_backgroundImage" style="background:url('{{ $imageSmall }}') no-repeat center center / cover;" ></div>
                                @else 
                                    <!-- các ảnh sau khi nào click mới load -->
                                    <img class="lazyloadAfter" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="{{ $productName }}" title="{{ $productName }}" style="filter:blur(8px);" />
                                @endif
                            @endif
                            <!-- rating và số lượng đã bán -->
                            {{-- <div class="wallpaperGridBox_item_image_rating">
                                @if(!empty($product->seo->rating_aggregate_star))
                                    <div><img src="{{ Storage::url('images/svg/icon_star.svg') }}" alt="đánh giá sản phẩm {{ $productName }}" title="đánh giá sản phẩm {{ $productName }}" />{{ $product->seo->rating_aggregate_star }}</div>
                                @endif
                                @if(!empty($product->sold))
                                    <div>Đã bán {{ $product->sold }}</div>
                                @endif
                            </div> --}}
                            {{-- <!-- icon giảm giá -->
                            @if(!empty($price->sale_off))
                                <div class="wallpaperGridBox_item_image_percent">- {{ $product->sale_off }}%</div>
                            @endif --}}
                            <!-- content -->
                            <div class="wallpaperGridBox_item_image_content">
                                <div class="wallpaperGridBox_item_image_content_title maxLine_2">
                                    @if(!empty($headingTitle)&&$headingTitle=='h2')
                                        <h2>{{ $productName }}</h2>
                                    @else 
                                        <h3>{{ $productName }}</h3>
                                    @endif
                                </div>
                                <div class="wallpaperGridBox_item_image_content_price">
                                    @php
                                        $p              = $i==0 ? $product->price : $price->price;
                                        $pOld           = $i==0 ? $product->price_before_promotion : $price->price_before_promotion;
                                        $xhtmlPrice     = $p.config('language.'.$language.'.currency');
                                        $xhtmlPrice     = \App\Helpers\Number::getFormatPriceByLanguage($p, $language);
                                        $xhtmlPriceOld  = null;
                                        if(!empty($p!=$pOld)){
                                            $pOld   = \App\Helpers\Number::getFormatPriceByLanguage($pOld, $language, false);
                                        }
                                        $xhtmlPriceOld  = '<span class="maxLine_1">'.$pOld.'</span>';
                                    @endphp
                                    <!-- giá -->
                                    <div>{!! $xhtmlPrice !!}</div>
                                    <!-- giá trước khuyến mãi -->
                                    {!! $xhtmlPriceOld !!}
                                </div>
                            </div>
                            <!-- background blur -->
                            <div class="wallpaperGridBox_item_image_backgroundBlur"></div>
                        </div>
                        <div class="wallpaperGridBox_item_image_background"></div>
                    </a>
                    <!-- thêm vào giỏ hành nhanh -->
                    <div class="wallpaperGridBox_item_action">
                        <div class="heart"></div>
                        @php
                            $keyPriceAll = [];
                            foreach($product->prices as $p) $keyPriceAll[] = $p->id;
                            $keyPriceAll = implode('-', $keyPriceAll);
                        @endphp
                        <div class="addToCart" onClick="addToCart('{{ $product->id }}', '{{ $keyPriceAll }}', 'all');"></div>
                    </div>
                    <!-- danh sách ảnh -->
                    <div class="wallpaperGridBox_item_imageList">
                        @php
                            $k                  = 1;
                        @endphp
                        @foreach($product->prices as $price)
                            @foreach($price->wallpapers as $wallpaper)
                                @php
                                    if($k==7) break;
                                    ++$k;
                                    $tag        = $tagBox ?? null;
                                    $keyIdFile  = 'js_changeOption_'.$tag.$price->id.$wallpaper->infoWallpaper->id;
                                    $selected   = null;
                                    if($keyIdPrice==$keyIdFile) $selected = 'selected';
                                    /* lấy ảnh mini */
                                    $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
                                @endphp
                                @if($k==7)
                                    <a href="/{{ $url }}" class="wallpaperGridBox_item_imageList_item {{ $selected }}">
                                        <div class="wallpaperGridBox_item_imageList_item_backgroundImage" style="background:url('{{ $imageMini }}') no-repeat center center / cover;"></div>
                                        <span class="wallpaperGridBox_item_imageList_item_count">+{{ $product->prices->count() - 5 }}</span>
                                    </a>
                                @else 
                                    <div class="wallpaperGridBox_item_imageList_item {{ $selected }}" onClick="changeOption('{{ $keyIdFile }}');">
                                        <div class="wallpaperGridBox_item_imageList_item_backgroundImage" style="background:url('{{ $imageMini }}') no-repeat center center / cover;"></div>
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
                @php
                    ++$i;
                @endphp
            @endif
        @endforeach
    @endforeach
</div>