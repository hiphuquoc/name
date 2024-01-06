
<div class="hide-766">
    @php
        $title = 'slider trang chủ '.config('main.company_name');
    @endphp
    <div id="js_setSlick" class="sliderBox js_setSlick">
        <img src="/storage/images/upload/slider-1-blur-type-manager-upload.webp" data-src="/storage/images/upload/slider-1-type-manager-upload.webp" alt="{{ $title }}" title="{{ $title }}" />
        <img src="/storage/images/upload/slider-1-blur-type-manager-upload.webp" data-src="/storage/images/upload/slider-1-type-manager-upload.webp" alt="{{ $title }}" title="{{ $title }}" />
    </div>
</div>
<div class="show-766">
    <div class="sliderBox js_setSlick">
        <img src="/storage/images/upload/slider-1-mobile-blur-type-manager-upload.webp" data-src="/storage/images/upload/slider-1-mobile-type-manager-upload.webp" alt="{{ $title }}" title="{{ $title }}" />
        <img src="/storage/images/upload/slider-1-mobile-blur-type-manager-upload.webp" data-src="/storage/images/upload/slider-1-mobile-type-manager-upload.webp" alt="{{ $title }}" title="{{ $title }}" />
    </div>
</div>
@push('scriptCustom')
    <script type="text/javascript">
        $(document).ready(function(){
            setSlick();
        })
        $(window).resize(function(){
            setSlick();
        })
        function setSlick(){
            $('.js_setSlick').slick({
                dots: true,
                arrows: true,
                prevArrow: '<button class="slick-arrow slick-prev" aria-label="Slide trước"><i class="fa-solid fa-arrow-left-long"></i></button>',
                nextArrow: '<button class="slick-arrow slick-next" aria-label="Slide tiếp theo"><i class="fa-solid fa-arrow-right-long"></i></button>',
                autoplay: true,
                infinite: true,
                autoplaySpeed: 5000,
                lazyLoad: 'ondemand',
                responsive: [
                    {
                        breakpoint: 567,
                        settings: {
                            arrows: false,
                        }
                    }
                ]
            });
        }
    </script>
@endpush