{{-- <!-- box -->
<div class="wallpaperGridBox viewFirstTime">
    @for($i=0;$i<5;++$i)
        <div class="wallpaperGridBox_itemBackground"></div>
    @endfor
</div>
<div class="wallpaperGridBox slickBox" style="display:none;">
    @if(!empty($wallpapers)&&$wallpapers->isNotEmpty())
        @if(!empty($viewBy)&&$viewBy=='each_set')
            @foreach($wallpapers as $wallpaper)
                @include('wallpaper.template.wallpaperItem', [
                    'product'   => $wallpaper, 
                    'language'  => $language,
                    'lazyload'  => true
                ])
            @endforeach
        @else
            @foreach($wallpapers as $wallpaper)
                @php
                    $link           = empty($language)||$language=='vi' ? '/'.$wallpaper->seo->slug_full : '/'.$wallpaper->en_seo->slug_full;
                    $wallpaperName  = $wallpaper->name ?? null;
                    $lazyload       = false;
                    if($loop->index>=$loaded) {
                        break;
                    }
                @endphp
                @foreach($wallpaper->prices as $price)
                    @foreach($price->wallpapers as $wallpaper)
                        @include('wallpaper.template.perWallpaperItem', [
                            'idProduct'     => $wallpaper->id,
                            'idPrice'       => $price->id,
                            'wallpaper'     => $wallpaper, 
                            'productName'   => $wallpaperName,
                            'link'          => $link,
                            'language'      => $language,
                            'lazyload'      => $lazyload
                        ])
                    @endforeach
                @endforeach
            @endforeach
        @endif 
    @endif
</div>
@push('scriptCustom')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            $('.viewFirstTime').css('display', 'none');
            $('.slickBox').css('display', 'flex');
            setSlick();
        });

        window.addEventListener('resize', function() {
            setSlick();
        });
        function setSlick(){
            $('.slickBox').slick({
                infinite: false,
                slidesToShow: 5,
                slidesToScroll: 5,
                arrows: true,
                prevArrow: '<button class="slick-arrow slick-prev" aria-label="Slide trước"><i class="fa-solid fa-angle-left"></i></button>',
                nextArrow: '<button class="slick-arrow slick-next" aria-label="Slide tiếp theo"><i class="fa-solid fa-angle-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 1799,
                        settings: {
                            infinite: false,
                            slidesToShow: 4,
                            slidesToScroll: 4,
                            arrows: true,
                        }
                    },
                    {
                        breakpoint: 1199,
                        settings: {
                            infinite: false,
                            slidesToShow: 2.6,
                            slidesToScroll: 2,
                            arrows: true,
                        }
                    },
                    {
                        breakpoint: 1023,
                        settings: {
                            infinite: false,
                            slidesToShow: 3.6,
                            slidesToScroll: 3,
                            arrows: true,
                        }
                    },
                    {
                        breakpoint: 990,
                        settings: {
                            infinite: false,
                            slidesToShow: 2.6,
                            slidesToScroll: 3,
                            arrows: true,
                        }
                    },
                    {
                        breakpoint: 577,
                        settings: {
                            infinite: false,
                            slidesToShow: 1.75,
                            slidesToScroll: 1,
                            arrows: true,
                        }
                    },
                ]
            });     
        }
    </script>
@endpush --}}