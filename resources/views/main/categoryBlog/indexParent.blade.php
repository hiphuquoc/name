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
    @include('main.schema.itemlist', ['data' => $blogs])
    <!-- END:: FAQ Schema -->

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
    @include('main.snippets.headerMain')
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
                        @include('main.categoryBlog.blogListLeftRight', compact('infoCategoryChilds'))
                        
                    </div>
                    <div class="pageContentWithSidebar_sidebar">
                        @include('main.categoryBlog.sidebar', compact('categoriesBlog'))
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts-custom')
    <script type="text/javascript">

        $(window).on('load', function () {
            
            buildTocContentSidebar('js_buildTocContentSidebar_element');

            /* fixed sidebar khi scroll */
            const elemt                 = $('.js_scrollFixed');
            const widthElemt            = elemt.parent().width();
            const positionTopElemt      = elemt.offset().top;
            const heightFooter          = 500;
            $(window).scroll(function(){
                const positionScrollbar = $(window).scrollTop();
                const scrollHeight      = $('body').prop('scrollHeight');
                const heightLimit       = parseInt(scrollHeight - heightFooter - elemt.outerHeight());
                if(positionScrollbar>positionTopElemt&&positionScrollbar<heightLimit){
                    elemt.addClass('scrollFixedSidebar').css({
                        'width'         : widthElemt,
                        'margin-top'    : '1.5rem'
                    });
                }else {
                    elemt.removeClass('scrollFixedSidebar').css({
                        'width'         : 'unset',
                        'margin-top'    : 0
                    });
                }
            });

        });
    </script>
@endpush