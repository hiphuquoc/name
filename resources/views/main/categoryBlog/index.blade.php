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
                        <!-- rating -->
                        {{-- @include('main.template.rating', compact('item')) --}}
                        <!-- Blog List -->
                        @include('main.categoryBlog.blogList', compact('blogs'))
                        
                    </div>
                    <div class="pageContentWithSidebar_sidebar">
                        @include('main.categoryBlog.sidebar', compact('categories', 'categoriesBlog'))
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts-custom')
    <script type="text/javascript">
        buildTocContentSidebar('js_buildTocContentSidebar_element');
    </script>
@endpush