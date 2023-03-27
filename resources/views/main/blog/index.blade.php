@extends('layouts.main')
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Title - Description - Social -->
    @include('main.schema.social', compact('item'))
    <!-- END:: Title - Description - Social -->

    <!-- STRAT:: Organization Schema -->
    @include('main.schema.organization')
    <!-- END:: Organization Schema -->

    <!-- STRAT:: Article Schema -->
    @include('main.schema.article', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: Article Schema -->
    @include('main.schema.creativeworkseries', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: FAQ Schema -->
    @include('main.schema.faq', ['data' => $item->faqs])
    <!-- END:: FAQ Schema -->

<!-- ===== END:: SCHEMA ===== -->
@endpush
@section('content')

    <!-- === START:: Header Top === -->
    @include('main.snippets.headerTop')
    <!-- === END:: Header Top === -->
    
    <!-- === START:: Header Main === -->
    @include('main.cacheHTML.create', [
        'content'   => 'main.snippets.headerMain'
    ])
    <!-- === END:: Header Main === -->

    @include('main.template.breadcrumb')

    <div class="contentBox">
        <div class="sectionBox">
            <div class="container">
                <div class="pageContentWithSidebar">
                    <div id="js_buildTocContentSidebar_element" class="pageContentWithSidebar_content">
                        <!-- title -->
                        <h1 class="titlePage">{{ $item->name ?? null }}</h1>
                        <!-- thông tin blog -->
                        <div class="headerInfoBlogBox">
                            <!-- rating -->
                            @include('main.template.rating', compact('item'))
                            <!-- thông tin -->
                            <div class="headerInfoBlogBox_info">
                                <span>
                                    <i class="fa-regular fa-clock"></i>
                                    {{ date('H:i, d/m/Y', strtotime($item->seo->created_at)) }}
                                </span>
                                <span>
                                    <i class="fa-solid fa-user-pen"></i>
                                    {{ $item->seo->user->name ?? config('main.author_name') }}
                                </span>
                            </div>
                        </div>
                        <!-- toc content -->
                        <div id="tocContentMain"></div>
                        <!-- Content -->
                        <div id="js_buildTocContentMain_element">
                            {!! $content ?? null !!}
                        </div>
                        <!-- related box -->
                        @include('main.blog.related', compact('blogRelates'))
                    </div>
                    <div class="pageContentWithSidebar_sidebar">
                        @include('main.categoryBlog.sidebar', compact('categories'))
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scriptCustom')
    <script type="text/javascript">
        $(window).ready(function(){
            buildTocContentMain('js_buildTocContentMain_element');
        })
    </script>
@endpush