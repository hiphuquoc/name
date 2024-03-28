@php
    /* lấy ảnh đại diện */
    $image      = !empty($itemSeo->image) ? '"'.env('APP_URL').Storage::url($itemSeo->image).'"' : null;
    /* trường hợp có gallery thì lấy gallery */
    /* chưa có trường hợp nên chưa xử lý */
    /* trường hợp có gallery sản phẩm thì lấy gallery sản phẩm */
    $flagHaveImage          = false;
    if(!empty($item->prices)&&$item->prices->isNotEmpty()){
        foreach($item->prices as $price){
            if(!empty($price->files)&&$price->files->isNotEmpty()){
                $flagHaveImage  = true;
                break;
            }
        }
    }
    if($flagHaveImage==true){
        $image          = null;
        $i              = 0;
        foreach($item->prices as $price){
            foreach($price->files as $file){
                if($i!=0) $image .= ', ';
                $image  .= '"'.env('APP_URL').Storage::url($file->file_path).'"';
                ++$i;
            }
        }
    }
    $title          = $itemSeo->seo_title ?? $item->seo->seo_title ?? null;
    $description    = $itemSeo->seo_description ?? $item->seo->seo_description ?? null;
    /* lấy giá theo ngôn ngữ */
    $tmp            = \App\Helpers\Number::getPriceByLanguage($lowPrice, $language);
    $lowPrice       = $tmp['number'];
    $currency       = $tmp['currency_code'];
    $tmp            = \App\Helpers\Number::getPriceByLanguage($highPrice, $language);
    $highPrice      = $tmp['number'];
@endphp
<script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "{{ $title }}",
        "url": "{{ URL::current() }}",
        "image":
            [
                {!! $image !!}
            ],
        "description": "{{ $description }}",
        "sku": "WW122023M{{ !empty($itemSeo->created_at) ? strtotime($itemSeo->created_at) : 00 }}YK/VN",
        "brand": {
            "@type": "Brand",
            "name": "{{ config('main.company_name') }}"
        },
        "review":
            {
                "@type": "Review",
                "reviewRating":
                    {
                        "@type": "Rating",
                        "ratingValue": "5"
                    },
                "author": {
                    "@type": "Organization",
                    "name": "{{ $itemSeo->rating_author_name ?? null }}",
                    "url": "{{ env('APP_URL') }}"
                }
            },
        "aggregateRating":
            {
                "@type": "AggregateRating",
                "ratingValue": "{{ $itemSeo->rating_aggregate_star ?? '4.8' }}",
                "reviewCount": "{{ $itemSeo->rating_aggregate_count ?? '172' }}",
                "bestRating": "5"
            },
        "offers":
            {
                "@type": "AggregateOffer",
                "url": "{{ URL::current() }}",
                "offerCount": "1",
                "priceCurrency": "{{ $currency ?? 'VND' }}",
                "lowPrice": "{{ $lowPrice ?? '50000' }}",
                "highPrice": "{{ $highPrice ?? '5000000' }}",
                "itemCondition": "https://schema.org/UsedCondition",
                "availability": "https://schema.org/InStock",
                "seller":
                    {
                        "@type": "Organization",
                        "name": "{{ $itemSeo->rating_author_name ?? config('main.author_name') }}",
                        "url": "{{ env('APP_URL') }}"
                    }
            }
    }
</script>
