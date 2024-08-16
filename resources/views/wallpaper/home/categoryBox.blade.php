<div class="contentBox">
    <div class="categoryGrid">
        <div class="categoryGrid_title">
            {{-- <input type="hidden" id="total" name="total" value="{{ $total ?? 0 }}" />
            <input type="hidden" id="loaded" name="loaded" value="{{ $data['items']->count() ?? 0 }}" /> --}}
            @php
                $titleBox = '';
                if($type=='category_info') $titleBox = config('language.'.$language.'.data.wallpaper_by_themes.'.env('APP_NAME'));
                if($type=='style_info') $titleBox = config('language.'.$language.'.data.wallpaper_by_styles.'.env('APP_NAME'));
                if($type=='event_info') $titleBox = config('language.'.$language.'.data.wallpaper_by_events.'.env('APP_NAME'));
            @endphp
            <h2>{{ $titleBox }}</h2>
        </div>
        <div class="categoryGrid_box">
            @foreach($categories as $category)
                @foreach($category->seos as $categorySeo)
                    @if(!empty($categorySeo->infoSeo->type)&&$categorySeo->infoSeo->type==$type) <!-- kiểm tra có ngôn ngữ -->
                        <div id="js_loadInfoCategory_{{ $category->id }}" class="categoryGrid_box_item js_loadInfoCategory" data-category_info_id="{{ $category->id }}">
                            <!-- load Ajax -->
                        </div>
                        @break
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>
</div>
@pushonce('scriptCustom')
    <script type="text/javascript">
        $(document).ready(function() {
            function loadVisibleCategories() {
                // Lấy vị trí hiện tại của scrollTop
                var scrollTop = $(window).scrollTop();
                var windowHeight = $(window).height();

                // Kiểm tra từng phần tử với class 'js_loadInfoCategory'
                $('.js_loadInfoCategory').each(function() {
                    var elementTop = $(this).offset().top;

                    // Kiểm tra nếu phần tử cách scrollTop 500px
                    if (elementTop < scrollTop + windowHeight + 500 && !$(this).hasClass('loaded')) {
                        // Gọi hàm loadInfoCategory và đánh dấu phần tử là đã load
                        let idCategory  = $(this).data('category_info_id');
                        let idWrite     = $(this).attr('id');
                        loadInfoCategory(idCategory, idWrite);
                        $(this).addClass('loaded'); // Để tránh load lại cùng một phần tử
                    }
                });
            }

            // Gọi hàm loadVisibleCategories khi trang vừa load
            loadVisibleCategories();

            // Gọi lại hàm loadVisibleCategories mỗi khi scroll
            $(window).on('scroll', function() {
                loadVisibleCategories();
            });
        });

        function loadInfoCategory(idCategory, idWrite) {
            let dataForm = {};
            dataForm.category_info_id = idCategory;
        
            const queryString = new URLSearchParams(dataForm).toString();
            fetch("/loadInfoCategory?" + queryString, {
                method: 'GET',
                mode: 'cors',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                $('#'+idWrite).html(data);
                /* lazyload ảnh trong khung nhìn */
                lazyload();
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }
    </script>
@endPushonce