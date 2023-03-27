<div class="categoryGridBox">
    <div id="categoryGridBox_1" class="container">
        @foreach($categories as $category)
            @php
                $title = $category->name ?? $category->seo->title ?? null;
            @endphp
            <a href="/{{ $category->seo->slug_full ?? null }}" class="categoryGridBox_item">
                <div class="categoryGridBox_item_icon">
                    <img src="{{ Storage::url($category->icon) }}" alt="{{ $title }}" title="{{ $title }}" />
                </div>
                <div class="categoryGridBox_item_title">
                    {{ $title }}
                </div>
            </a>
        @endforeach
        {{-- <a href="#" class="categoryGridBox_item">
            <div class="categoryGridBox_item_icon">
                <img src="{{ Storage::url('images/svg/icon-healthy.png') }}" alt="" title="" />
            </div>
            <div class="categoryGridBox_item_title">
                Dinh dưỡng và sức khỏe
            </div>
        </a>
        <a href="#" class="categoryGridBox_item">
            <div class="categoryGridBox_item_icon">
                <img src="{{ Storage::url('images/svg/icon-make-up.png') }}" alt="" title="" />
            </div>
            <div class="categoryGridBox_item_title">
                Trang điểm
            </div>
        </a>
        <a href="#" class="categoryGridBox_item">
            <div class="categoryGridBox_item_icon">
                <img src="{{ Storage::url('images/svg/icon-skincare.png') }}" alt="" title="" />
            </div>
            <div class="categoryGridBox_item_title">
                Chăm sóc da
            </div>
        </a>
        <a href="#" class="categoryGridBox_item">
            <div class="categoryGridBox_item_icon">
                <img src="{{ Storage::url('images/svg/icon-body.png') }}" alt="" title="" />
            </div>
            <div class="categoryGridBox_item_title">
                Chăm sóc cơ thể
            </div>
        </a>
        <a href="#" class="categoryGridBox_item">
            <div class="categoryGridBox_item_icon">
                <img src="{{ Storage::url('images/svg/icon-baby.png') }}" alt="" title="" />
            </div>
            <div class="categoryGridBox_item_title">
                Em bé
            </div>
        </a>
        <a href="#" class="categoryGridBox_item">
            <div class="categoryGridBox_item_icon">
                <img src="{{ Storage::url('images/svg/icon-perfume-bottle.png') }}" alt="" title="" />
            </div>
            <div class="categoryGridBox_item_title">
                Hương thơm
            </div>
        </a>
        <a href="#" class="categoryGridBox_item">
            <div class="categoryGridBox_item_icon">
                <img src="{{ Storage::url('images/svg/icon-hair-washing.png') }}" alt="" title="" />
            </div>
            <div class="categoryGridBox_item_title">
                Chăm sóc tóc
            </div>
        </a>
        <a href="#" class="categoryGridBox_item">
            <div class="categoryGridBox_item_icon">
                <img src="{{ Storage::url('images/svg/icon-cleaning.png') }}" alt="" title="" />
            </div>
            <div class="categoryGridBox_item_title">
                Gia dụng
            </div>
        </a>
        <a href="#" class="categoryGridBox_item">
            <div class="categoryGridBox_item_icon">
                <img src="{{ Storage::url('images/svg/icon-gift.png') }}" alt="" title="" />
            </div>
            <div class="categoryGridBox_item_title">
                Quà tặng
            </div>
        </a> --}}
        
    </div>
</div>
@push('scriptCustom')
    <script type="text/javascript">
        $(document).ready(function(){
            setSlickcategoryGridBox_1();
        })
        $(window).resize(function(){
            setSlickcategoryGridBox_1();
        })
        function setSlickcategoryGridBox_1(){
            const _slide = $('#categoryGridBox_1');
            if (_slide.length <= 0) return;
            _slide.slick({
                infinite: false,
                slidesToShow: 8,
                slidesToScroll: 8,
                dots: false,
                arrows: true,
                prevArrow: '<button class="slick-arrow slick-prev" aria-label="Slide trước"><i class="fa-solid fa-angle-left"></i></button>',
                nextArrow: '<button class="slick-arrow slick-next" aria-label="Slide tiếp theo"><i class="fa-solid fa-angle-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 1199,
                        settings: {
                            slidesToShow: 7,
                            slidesToScroll: 7
                        }
                    },
                    {
                        breakpoint: 990,
                        settings: {
                            slidesToShow: 6,
                            slidesToScroll: 6
                        }
                    },
                    {
                        breakpoint: 766,
                        settings: {
                            slidesToShow: 5,
                            slidesToScroll: 5
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 4
                        }
                    },
                    {
                        breakpoint: 419,
                        settings: {
                            slidesToShow: 3.5,
                            slidesToScroll: 3
                        }
                    }
                ]
            });
        }
    </script>
@endpush