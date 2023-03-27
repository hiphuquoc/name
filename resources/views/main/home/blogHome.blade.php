@if(!empty($blogs)&&$blogs->isNotEmpty())
    <div class="blogHomeBox">
        <div class="blogHomeBox_title">
            <a href="/blog-lam-dep"><h2>Blog làm đẹp</h2></a>
        </div>
        <div class="blogHomeBox_description">
            Là phụ nữ, nhất định phải xinh đẹp và tự tin. Hãy cùng Cỏ mềm HomeLab khám phá những bí quyết làm đẹp thú vị nhé!
        </div>
        <div class="blogHomeBox_box js_setSlickBlogHomeBox">
            @foreach($blogs as $blog)
                @php
                    $title      = $blog->name ?? $blog->seo->title ?? null;
                @endphp
                <a href="/{{ $blog->seo->slug_full ?? null }}" class="blogHomeBox_box_item" title="{{ $title }}">
                    <img src="{{ Storage::url(config('image.loading_main_gif_small')) }}" data-src="{{ Storage::url($blog->seo->image_small) }}" alt="{{ $title }}" title="{{ $title }}" />
                    <div class="blogHomeBox_box_item_content">
                        <div class="blogHomeBox_box_item_content_title">
                            <h3 class="maxLine_2">{{ $title }}</h3>
                        </div>
                    </div>
                    <div class="blogHomeBox_box_item_background"></div>
                </a>
            @endforeach
        </div>
    </div>
@endif
@push('scriptCustom')
    <script type="text/javascript">
        $(document).ready(function(){
            setSlickBlogHomeBox();
        })
        $(window).resize(function(){
            setSlickBlogHomeBox();
        })
        function setSlickBlogHomeBox(){
            $('.js_setSlickBlogHomeBox').each(function(){
                if ($(this).length <= 0) return;
                $(this).slick({
                    infinite: false,
                    slidesToShow: 5,
                    slidesToScroll: 5,
                    dots: false,
                    arrows: true,
                    prevArrow: '<button class="slick-arrow slick-prev" aria-label="Slide trước"><i class="fa-solid fa-angle-left"></i></button>',
                    nextArrow: '<button class="slick-arrow slick-next" aria-label="Slide tiếp theo"><i class="fa-solid fa-angle-right"></i></button>',
                    responsive: [
                        {
                            breakpoint: 1199,
                            settings: {
                                slidesToShow: 4,
                                slidesToScroll: 4
                            }
                        },
                        {
                            breakpoint: 990,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2
                            }
                        },
                        {
                            breakpoint: 576,
                            settings: {
                                slidesToShow: 1.5,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 419,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
            });
        }
    </script>
@endpush