@php
    $title              = $itemSeo->seo_title ?? $item->seo->seo_title ?? config('main.description');
    $description        = $itemSeo->seo_description ?? $item->seo->seo_description ?? null;
    $url                = $itemSeo->link_canonical ?? $itemSeo->slug_full ?? $item->seo->link_canonical ?? $item->seo->slug_full;
    $urlFull            = !empty($url) ? env('APP_URL').'/'.$url : env('APP_URL');
    $image              = !empty($item->seo->image) ? env('APP_URL').Storage::url($item->seo->image) : env('APP_URL').config('admin.images.default_750x460');
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}" />
<link rel="canonical" href="{{ $urlFull }}" />
<meta property="og:locale" content="vi_VN" />
<meta property="article:published_time" content="{{ date('c', strtotime($item->seo->created_at)) }}" />
<meta property="article:modified_time" content="{{ date('c', strtotime($item->seo->updated_at)) }}" />
<meta property="og:title" content="{{ $title }}" />
<meta property="og:description" content="{{ $description }}" />
<meta property="og:image" content="{{ $image }}" />
<meta property="og:image:secure_url" content="{{ $image }}" />
<meta property="og:image:type" content="image/webp" /> <!-- Định dạng của ảnh -->
<meta property="og:image:width" content="600" /> <!-- Kích thước ảnh: chiều rộng -->
<meta property="og:image:height" content="600" /> <!-- Kích thước ảnh: chiều cao -->
<meta property="og:image:alt" content="{{ $title }}" />
<meta property="og:url" content="{{ $urlFull }}" />
<meta property="og:site_name" content="{{ $title }}" />
<meta property="og:type" content="website" />
@if(!empty($lowPrice)&&!empty($highPrice))
    @if($lowPrice>=$highPrice)
        <meta property="og:price:amount" content="{{ $lowPrice }}" />
    @else 
        <meta property="og:price:amount:minimum" content="{{ $lowPrice }}" />
        <meta property="og:price:amount:maximum" content="{{ $highPrice }}" />
    @endif
    <meta property="og:price:currency" content="{{ config('language.'.$language.'.currency') }}" />
@endif
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $title }}" />
<meta name="twitter:description" content="{{ $description }}" />
<meta name="twitter:creator" content="{{ $item->seo->rating_author_name ?? config('main.author_name') }}" />
<meta name="twitter:image" content="{{ $image }}" />