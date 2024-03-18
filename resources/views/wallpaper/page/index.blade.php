@extends('layouts.wallpaper')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.social', compact('item'))
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
    {{-- <div style="overflow:hidden;"> --}}
        
        

        {{-- <!-- share social -->
        @include('wallpaper.template.shareSocial') --}}

        
        <!-- Gallery và Product detail -->
        <div class="container">
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
                    {{-- <div class="pageContentWithSidebar_sidebar">
                        <!-- trang liên quan (nhiều loại) -->
                        @if(!empty($typePages)&&$typePages->isNotEmpty())
                            @foreach($typePages as $typePage)
                                <div class="sidebarSectionBox">
                                    <div class="sidebarSectionBox_title">
                                        @if(empty($language)||$language=='vi')
                                            <h2>{{ $typePage[0]->type->name }}</h2>
                                        @else
                                            <h2>Policies & Terms</h2>
                                        @endif
                                    </div>
                                    <div class="sidebarSectionBox_box">
                                    @foreach($typePage as $page)
                                        @php
                                            $selected       = null;
                                            if(empty($language)||$language=='vi'){
                                                $title          = $page->name ?? $page->seo->title ?? null;
                                                $urlPageFull    = env('APP_URL').'/'.$page->seo->slug_full;
                                            }else {
                                                $title          = $page->en_name ?? $page->en_seo->title ?? null;
                                                $urlPageFull    = env('APP_URL').'/'.$page->en_seo->slug_full;
                                            }
                                            if($urlPageFull==URL::current()) $selected = 'selected';
                                        @endphp
                                        <a href="{{ $urlPageFull }}" title="{{ $title }}" class="sidebarSectionBox_box_item {{ $selected }}">
                                            <i class="fa-solid fa-chevron-right"></i><h3>{{ $title }}</h3>
                                        </a>
                                    @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div> --}}
                </div>
            </div>
        </div>
    {{-- </div> --}}
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