@extends('layouts.wallpaper')
@push('cssFirstView')
    @php
        $manifest           = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        $cssFirstView       = $manifest['resources/sources/main/page-first-view.scss']['file'];
    @endphp
    <style type="text/css">
        {!! file_get_contents(asset('build/' . $cssFirstView)) !!}
    </style>
@endpush
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Title - Description - Social -->
    @php
        $highPrice          = 0;
        $lowPrice           = 0;
    @endphp
    @include('wallpaper.schema.social', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Title - Description - Social -->

    <!-- STRAT:: Organization Schema -->
    @include('wallpaper.schema.organization')
    <!-- END:: Organization Schema -->

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.article', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.creativeworkseries', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: FAQ Schema -->
    @include('wallpaper.schema.faq', ['data' => $item->faqs])
    <!-- END:: FAQ Schema -->
<!-- ===== END:: SCHEMA ===== -->
@endpush
@section('content')
    <div class="breadcrumbMobileBox">
        @include('wallpaper.template.breadcrumb')
    </div>

    <div class="contentBox">
        <div class="pageContentWithSidebar">
            <div class="pageContentWithSidebar_content">
                <h1>{{ $itemSeo->title ?? $item->seo->title ?? null }}</h1>
                <!-- Nội dung -->
                @if(!empty($itemSeo->contents))
                    @php
                        $xhtmlContent = '';
                        foreach($itemSeo->contents as $content) $xhtmlContent .= $content->content;
                    @endphp
                    {!! $xhtmlContent !!}
                @endif
            </div>
        </div>
    </div>
@endsection
@push('modal')

@endpush
@push('bottom')
    <!-- Header bottom -->
    @include('wallpaper.snippets.headerBottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        
    </script>
@endpush