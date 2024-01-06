<div class="categoryWithBannerBox">
    <div class="container">
        <div class="categoryWithBannerBox_title">
            <h2>{{ $title ?? null }}</h2>
        </div>
        <div class="categoryWithBannerBox_box">
            <div class="categoryWithBannerBox_box_banner" style="background:url('https://phytexfarma.com/wp-content/uploads/elementor/thumbs/banner-doc-trai-1-200x690px-pzkxge1lc82nhlsmwoz2m6yu3z30i6r1g170cxje9s.jpg') no-repeat;background-position:center top;background-size:100% auto;"></div>
            <div class="categoryWithBannerBox_box_product">
                @include('main.template.productGrid', compact('products'))

            </div>
        </div>
    </div>
</div>
@push('scriptCustom')
    <script type="text/javascript">
        $(document).ready(function(){
            $('.categoryWithBannerBox .productGridBox').slick({
                infinite: false,
                slidesToShow: 4,
                slidesToScroll: 4,
                prevArrow:"<button type='button' class='slick-prev pull-left'><i class='fa fa-angle-left' aria-hidden='true'></i></button>",
                nextArrow:"<button type='button' class='slick-next pull-right'><i class='fa fa-angle-right' aria-hidden='true'></i></button>",
                arrows: true,
                // responsive: [
                //     {
                //         breakpoint: 991,
                //         settings: {
                //             infinite: false,
                //             slidesToShow: 3.2,
                //             slidesToScroll: 3,
                //             arrows: true,
                //         }
                //     },
                //     {
                //         breakpoint: 767,
                //         settings: {
                //             infinite: false,
                //             slidesToShow: 2.4,
                //             slidesToScroll: 2,
                //             arrows: false,
                //         }
                //     },
                //     {
                //         breakpoint: 567,
                //         settings: {
                //             infinite: false,
                //             slidesToShow: 1.3,
                //             slidesToScroll: 1,
                //             arrows: false,
                //         }
                //     }
                // ]
            });
            setupSlick();
            $(window).resize(function(){
                setupSlick();
            })

            function setupSlick(){
                setTimeout(function(){
                    $('.blogBox .slick-prev').html('<i class="fas fa-chevron-left"></i>');
                    $('.blogBox .slick-next').html('<i class="fas fa-chevron-right"></i>');
                }, 0);
            }
        });

    </script>
@endpush