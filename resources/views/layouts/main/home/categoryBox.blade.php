<div class="categoryBox">
    <div class="container">
        <div class="categoryBox_title">
            <h2>{{ $title ?? null }}</h2>
        </div>
        <div class="categoryBox_box">
        @include('main.template.productGrid', compact('products'))
        </div>
    </div>
</div>
@push('scriptCustom')
    <script type="text/javascript">
        $(document).ready(function(){
            $('.categoryBox .productGridBox').slick({
                infinite: false,
                slidesToShow: 5,
                slidesToScroll: 5,
                prevArrow:"<button type='button' class='slick-prev pull-left'><i class='fa fa-angle-left' aria-hidden='true'></i></button>",
                nextArrow:"<button type='button' class='slick-next pull-right'><i class='fa fa-angle-right' aria-hidden='true'></i></button>",
                arrows: true,
                responsive: [
                    {
                        breakpoint: 1199,
                        settings: {
                            infinite: false,
                            slidesToShow: 4.5,
                            slidesToScroll: 4
                        }
                    },
                    {
                        breakpoint: 990,
                        settings: {
                            infinite: false,
                            slidesToShow: 3.5,
                            slidesToScroll: 3
                        }
                    },
                    {
                        breakpoint: 766,
                        settings: {
                            infinite: false,
                            slidesToShow: 2.5,
                            slidesToScroll: 2
                        }
                    },
                    {
                        breakpoint: 577,
                        settings: {
                            infinite: false,
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    }
                ]
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