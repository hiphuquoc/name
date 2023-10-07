@php
    if(empty($language)||$language=='vi'){
        $title          = $item->seo->seo_title ?? $item->seo->title ?? config('main.description');
        $description    = $item->seo->seo_description ?? $item->seo->description ?? null;
        $url            = $item->seo->link_canonical ?? $item->seo->slug_full;
    }else {
        $title          = $item->en_seo->seo_title ?? $item->en_seo->title ?? config('main.description');
        $description    = $item->en_seo->seo_description ?? $item->en_seo->description ?? null;
        $url            = $item->en_seo->link_canonical ?? $item->en_seo->slug_full;
    }
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
    @if(empty($language)||$language=='vi')
        <meta property="og:price:currency" content="VNĐ" />
    @else
        <meta property="og:price:currency" content="USD" />
    @endif
@endif
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $title }}" />
<meta name="twitter:description" content="{{ $description }}" />
<meta name="twitter:creator" content="{{ $item->seo->rating_author_name ?? config('main.author_name') }}" />
<meta name="twitter:image" content="{{ $image }}" />