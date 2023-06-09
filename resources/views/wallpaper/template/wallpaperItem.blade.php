@php
    $allImage           = new \Illuminate\Database\Eloquent\Collection;
    $i                  = 0;
    foreach($product->prices as $price){
        foreach($price->files as $file){
            $allImage[$i]       = $file;
            ++$i;
        }
    }
    if(!empty($language)&&$language=='en'){
        $productName        = $product->en_name ?? $product->en_seo->title ?? null;
    }else {
        $productName        = $product->name ?? $product->seo->title ?? null;
    }
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
<div class="wallpaperGridBox_item" data-key="{{ $dataFilter }}" data-price="{{ $product->prices[0]->price }}">
    @php
        $i = 0;
    @endphp
    @foreach($product->prices as $price)
        @foreach($price->files as $file)
            <!-- one price && file -->
            {{-- .\App\Helpers\Charactor::randomString(10) --}}
            @php
                $tag        = $tagBox ?? null;
                $keyIdPrice = 'js_changeOption_'.$tag.$price->id.$file->id;
                /* lấy ảnh mini */
                $fileInfo   = pathinfo($file->file_path);
                $imageSmall = Storage::url($fileInfo['dirname'].'/'.$fileInfo['filename'].'-small'.'.'.$fileInfo['extension']);
                /* đường dẫn */
                $url        = !empty($language)&&$language=='en'&&!empty($product->en_seo->slug_full) ? $product->en_seo->slug_full : $product->seo->slug_full;
            @endphp
            <div id="{{ $keyIdPrice }}" class="{{ $i==0 ? 'show' : 'hide' }}">
                <a href="/{{ $url }}?product_price_id={{ $price->id }}" class="wallpaperGridBox_item_image">
                    <div class="zIndexHide">
                        
                        <!-- ảnh -->

                        <!-- xử lý loadajax -->
                        @if(!empty($type)&&$type=='ajax')
                            <img src="{{ $imageSmall }}" alt="{{ $productName }}" title="{{ $productName }}" />
                        @else 
                            @if($i==0)
                                <div class="wallpaperGridBox_item_image_backgroundImage lazyload" data-src="{{ $imageSmall }}" style="background:url('{{ $imageSmall }}') no-repeat center center / cover;filter:blur(5px);"></div>
                            @else 
                                <!-- các ảnh sau khi nào click mới load -->
                                <img data-src="{{ $imageSmall }}" alt="{{ $productName }}" title="{{ $productName }}" />
                            @endif
                        @endif
                        <!-- rating và số lượng đã bán -->
                        <div class="wallpaperGridBox_item_image_rating">
                            @if(!empty($product->seo->rating_aggregate_star))
                                <div><img src="{{ Storage::url('images/svg/icon_star.svg') }}" alt="đánh giá sản phẩm {{ $productName }}" title="đánh giá sản phẩm {{ $productName }}" />{{ $product->seo->rating_aggregate_star }}</div>
                            @endif
                            {{-- <div>Đã bán {{ $product->sold }}</div> --}}
                            {{-- @if(!empty($product->sold))
                                <div>Đã bán {{ $product->sold }}</div>
                            @endif --}}
                        </div>
                        <!-- icon giảm giá -->
                        @if(!empty($price->sale_off))
                            <div class="wallpaperGridBox_item_image_percent">- {{ $price->sale_off }}%</div>
                        @endif
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
                                <!-- giá -->
                                <div>{!! number_format($price->price).config('main.currency_unit') !!}</div>
                                <!-- giá trước khuyến mãi -->
                                @if(!empty($price->price!=$price->price_before_promotion))
                                    <span class="maxLine_1">{{ number_format($price->price_before_promotion) }}{!! config('main.currency_unit') !!}</span>
                                @endif
                            </div>
                        </div>
                        <!-- background blur -->
                        <div class="wallpaperGridBox_item_image_backgroundBlur"></div>
                    </div>
                    <div class="wallpaperGridBox_item_image_background"></div>
                </a>
                <!-- danh sách ảnh -->
                <div class="wallpaperGridBox_item_imageList">
                    @foreach($allImage as $image)
                        @php
                            $tag        = $tagBox ?? null;
                            $keyIdFile  = 'js_changeOption_'.$tag.$image->attachment_id.$image->id;
                            $selected   = null;
                            if($keyIdPrice==$keyIdFile) $selected = 'selected';
                            if($loop->index==5) break;
                            /* lấy ảnh mini */
                            $fileInfo   = pathinfo($image->file_path);
                            $imageMini  = Storage::url($fileInfo['dirname'].'/'.$fileInfo['filename'].'-mini'.'.'.$fileInfo['extension']);
                        @endphp
                        <div class="wallpaperGridBox_item_imageList_item {{ $selected }}" onClick="changeOption('{{ $keyIdFile }}');">
                            @if(!empty($type)&&$type=='lazyload')
                                <!-- lazy load image list -->
                                <img src="{{ $imageMini }}" alt="loading cart" title="loading cart" />
                            @else
                                <div class="wallpaperGridBox_item_imageList_item_backgroundImage" style="background:url('{{ $imageMini }}') no-repeat center center / cover;"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @php
                ++$i;
            @endphp
        @endforeach
    @endforeach
</div>