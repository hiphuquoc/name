@if(!empty($products)&&$products->isNotEmpty())
    <div id="js_filterProduct_show" class="productGridBox">
        @foreach($products as $product)
            @php
                $allImage           = new \Illuminate\Database\Eloquent\Collection;
                $i                  = 0;
                foreach($product->prices as $price){
                    foreach($price->files as $file){
                        $allImage[$i]       = $file;
                        ++$i;
                    }
                }
                $productName        = $product->name ?? $product->seo->title ?? null;
                /* data filter => data-filter này phải gộp theo thứ tự menu filter từ trên xuống của giá trị (để xây dựng partern filter chính xác) 
                    => filter theo danh mục
                    => filter theo nhãn hàng
                    => filter theo giá
                */
                $dataFilter         = 'tat-ca-danh-muc tat-ca-nhan-hang';
                $i                  = 0;
                foreach($product->categories as $category){
                    if(!empty($category->infoCategory)) $dataFilter     .= ' '.$category->infoCategory->seo->slug;
                    ++$i;
                }
                /* gộp thêm của brand vào */
                $dataFilter         .= ' '.$product->brand->seo->slug;
            @endphp 
            <div class="productGridBox_item" data-key="{{ $dataFilter }}" data-price="{{ $product->prices[0]->price }}">
                @php
                    $i = 0;
                @endphp
                @foreach($product->prices as $price)
                    @foreach($price->files as $file)
                        <!-- one price && file -->
                        @php
                            $keyIdPrice = 'js_changeOption_'.$price->id.$file->id;
                            /* lấy ảnh small */
                            $fileInfo   = pathinfo($file->file_path);
                            $imageSmall = $fileInfo['dirname'].'/'.$fileInfo['filename'].'-small'.'.'.$fileInfo['extension'];
                        @endphp
                        <div id="{{ $keyIdPrice }}" class="{{ $i==0 ? 'show' : 'hide' }}">
                            <a href="/{{ $product->seo->slug_full }}?product_price_id={{ $price->id }}" class="productGridBox_item_image">
                                <!-- ảnh -->
                                <img src="{{ Storage::url(config('image.loading_main_gif_small')) }}" data-src="{{ Storage::url($imageSmall) }}" alt="{{ $productName }}" title="{{ $productName }}" />
                                <!-- rating và số lượng đã bán -->
                                <div class="productGridBox_item_image_rating">
                                    @if(!empty($product->seo->rating_aggregate_star))
                                        <div><img src="{{ Storage::url('images/svg/icon_star.svg') }}" alt="đánh giá sản phẩm {{ $productName }}" title="đánh giá sản phẩm {{ $productName }}" />{{ $product->seo->rating_aggregate_star }}</div>
                                    @endif
                                    @if(!empty($product->sold))
                                        <div>Đã bán {{ $product->sold }}</div>
                                    @endif
                                </div>
                                <!-- icon giảm giá -->
                                @if(!empty($price->sale_off))
                                    <div class="productGridBox_item_image_percent">- {{ $price->sale_off }}%</div>
                                @endif
                            </a>
                            <!-- danh sách ảnh -->
                            <div class="productGridBox_item_imageList">
                                @foreach($allImage as $image)
                                    @php
                                        $keyIdFile  = 'js_changeOption_'.$image->attachment_id.$image->id;
                                        $selected   = null;
                                        if($keyIdPrice==$keyIdFile) $selected = 'selected';
                                        if($loop->index==5) break;
                                        /* lấy ảnh mini */
                                        $fileInfo   = pathinfo($image->file_path);
                                        $imageMini  = $fileInfo['dirname'].'/'.$fileInfo['filename'].'-mini'.'.'.$fileInfo['extension'];
                                    @endphp
                                    <div class="productGridBox_item_imageList_item {{ $selected }}" onClick="changeOption('{{ $keyIdFile }}');">
                                        <img src="{{ Storage::url(config('image.loading_main_gif_small')) }}" data-src="{{ Storage::url($imageMini) }}" alt="loading cart" title="loading cart" />
                                    </div>
                                @endforeach
                            </div>
                            <!-- content -->
                            <div class="productGridBox_item_content">
                                <a href="/{{ $product->seo->slug_full }}?product_price_id={{ $price->id }}" class="productGridBox_item_content_title maxLine_2">
                                    @if(!empty($headingTitle)&&$headingTitle=='h2')
                                        <h2>{{ $productName }}</h2>
                                    @else 
                                        <h3>{{ $productName }}</h3>
                                    @endif
                                </a>
                                <div class="productGridBox_item_content_price">
                                    <!-- giá -->
                                    <div>{!! number_format($price->price).config('main.currency_unit') !!}</div>
                                    <!-- giá trước khuyến mãi -->
                                    @if(!empty($price->price!=$price->price_before_promotion))
                                        <span class="maxLine_1">{{ number_format($price->price_before_promotion) }}{!! config('main.currency_unit') !!}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @php
                            ++$i;
                        @endphp
                    @endforeach
                @endforeach
            </div>
        @endforeach
    </div>
@else 
    <div>Không có sản phẩm phù hợp!</div>
@endif