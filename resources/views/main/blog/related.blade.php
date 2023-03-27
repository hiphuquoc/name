@if(!empty($blogRelates)&&$blogRelates->isNotEmpty())
    <div class="relatedBox">
        <div class="relatedBox_title">
            Bài viết liên quan
        </div>
        <div class="relatedBox_box">
            @foreach($blogRelates as $blog)
                @php
                    $title  = $blog->name ?? $blog->seo->title ?? $blog->seo->seo_title ?? null;
                    $image  = !empty($blog->seo->image_small)&&file_exists(Storage::path($blog->seo->image_small)) ? Storage::url($blog->seo->image_small) : config('image.default');
                @endphp
                <div class="relatedBox_box_item">
                    <div class="relatedBox_box_item_image">
                        <a href="{{ !empty($blog->seo->slug_full) ? url($blog->seo->slug_full) : url($blog->slug_full) }}" title="{{ $title }}">
                            <img src="{{ Storage::url(config('image.loading_main_gif_small')) }}" data-src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
                        </a>
                    </div>
                    <div class="relatedBox_box_item_content">
                        <a href="{{ !empty($blog->seo->slug_full) ? url($blog->seo->slug_full) : url($blog->slug_full) }}" title="{{ $title }}" class="relatedBox_box_item_content_title">
                            <h3 class="maxLine_2">
                                {{ $blog->seo->title ?? $blog->name ?? null }}
                            </h3>
                        </a>
                        @if(!empty($blog->seo->updated_at))
                            <div class="relatedBox_box_item_content_time">
                                <i class="fa-regular fa-calendar-days"></i>{{ date('H:i\, d/m/Y', strtotime($blog->seo->updated_at)) }}
                            </div>
                        @endif
                        <div class="relatedBox_box_item_content_des maxLine_3">
                            {{ $blog->seo->description ?? $blog->description ?? null }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @push('scriptCustom')
        @if($blogRelates->count()>=4)
            <script type="text/javascript">
                $(document).ready(function(){
                    $('.relatedBox_box').slick({
                        infinite: false,
                        slidesToShow: 2.7,
                        slidesToScroll: 3,
                        arrows: true,
                        prevArrow: '<button class="slick-arrow slick-prev" aria-label="Slide trước"><i class="fa-solid fa-angle-left"></i></button>',
                        nextArrow: '<button class="slick-arrow slick-next" aria-label="Slide tiếp theo"><i class="fa-solid fa-angle-right"></i></button>',
                        responsive: [
                            {
                                breakpoint: 767,
                                settings: {
                                    infinite: false,
                                    slidesToShow: 2.4,
                                    slidesToScroll: 2,
                                    arrows: false,
                                }
                            },
                            {
                                breakpoint: 568,
                                settings: {
                                    infinite: false,
                                    slidesToShow: 1.4,
                                    slidesToScroll: 1,
                                    arrows: false,
                                }
                            }
                        ]
                    });
                    // setupSlick();
                    // $(window).resize(function(){
                    //     setupSlick();
                    // })
                    // function setupSlick(){
                    //     setTimeout(function(){
                    //         $('.relatedBox_box .slick-prev').html('<i class="fas fa-chevron-left"></i>');
                    //         $('.relatedBox_box .slick-next').html('<i class="fas fa-chevron-right"></i>');
                    //     }, 0);
                    // }

                });

            </script>
        @endif
    @endpush
@endif