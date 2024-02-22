<input type="hidden" id="total" name="total" value="{{ $total }}" />
<input type="hidden" id="loaded" name="loaded" value="{{ $loaded ?? 0 }}" />
<input type="hidden" id="topLoad" name="topLoad" value="" />
<input type="hidden" id="idNot" name="idNot" value="{{ $idNot ?? 0 }}" />
<input type="hidden" id="arrayIdCategory" name="arrayIdCategory" value="{{ json_encode($arrayIdCategory) }}" />
<div class="freeWallpaperBox">
    @if($total>0)
        @foreach($wallpapers as $wallpaper)
            @include('wallpaper.category.item', [
                'wallpaper' => $wallpaper,
                'language'  => $language,
                'user'      => $user ?? null
            ])
        @endforeach
    @else 
        <div>Không có kết quả phù hợp!</div>
    @endif
</div>
@push('scriptCustom')
    <script type="text/javascript">
        $(window).ready(function(){
            /* lazyload image */
            lazyload();
            /* load more */
            loadFreeWallpaperMore(50);
            $(window).scroll(function(){
                loadFreeWallpaperMore(50);
            })
            /* tính lại khi resize */
            setTimeout(() => {
                setViewAllImage();
            }, 500);
            $(window).resize(function(){
                setViewAllImage();
            })          
        })

        /* hiển thị box cảm xúc khi nhấn vào icon */ 
        function showBoxFeeling(element) {
            // Tìm phần tử .freeWallpaperBox_item_box
            var boxItem = $(element).closest('.freeWallpaperBox_item_box');
            // Toggle class active cho .feeling
            console.log(boxItem.find('.feeling'));
            boxItem.find('.feeling').attr('style', 'display:flex !important;');
        }
        /* thả cảm xúc */
        function setFeelingFreeWallpaper(element, idFreeWallpaper, type){
            $.ajax({
                url         : '{{ route("ajax.setFeelingFreeWallpaper") }}',
                type        : 'get',
                dataType    : 'json',
                data        : {
                    type,
                    free_wallpaper_info_id : idFreeWallpaper
                },
                success     : function(response){
                    $(element).closest('.feeling').css('display', 'none');
                    /* tải lại box */ 
                    var box     = $(element).closest('.freeWallpaperBox_item');
                    var idBox   = box.attr('id');
                    loadOneFreeWallpaper(idFreeWallpaper, idBox);
                }
            });
        }
        /* phần wallpaper */
        function setViewAllImage(dataJson){
            const box = $('.freeWallpaperBox');
            /* tính số cột đang hiển thị */ 
            const row = calculateColumns();
            /* duyệt và set vị trí */
            var dataColumn          = {};
            box.children().each(function(index) {
                const idWallpaper = $(this).data('id');
                /* set vị trí của từng ảnh */
                const domItem   = $('#js_calculatorPosition_item_'+idWallpaper);
                if(index>=row) setViewPerImage(domItem, index, row);
                /* tính tổng chiều cao của box */
                const itemHeight = domItem.outerHeight(true);
                const columnIndex = index % row;
                if (!dataColumn[columnIndex]) dataColumn[columnIndex] = 0;
                dataColumn[columnIndex] += parseInt(itemHeight + 16);
            });
            /* set height cho toàn box -> lấy height của cột cao nhất (vì các phần tử con position: absolute; nên phải lấy theo cách này) */
            const maxHeight = Math.round(Math.max(...Object.values(dataColumn)));
            $('.freeWallpaperBox').css('min-height', maxHeight+'px');
            /* set khi load gần đến cột ngắn nhất load tiếp */ 
            const minHeight = Math.round(Math.min(...Object.values(dataColumn)));
            $('#topLoad').val(minHeight);
        }
        function setViewPerImage(domItem, indexItem, row) {
            const positionItem  = domItem.position();
            const parent        = $(domItem).parent();
            // Tìm domItemTop cách domItem 3 phần tử về phía trên
            const index         = domItem.index();
            const domItemTop    = parent.children().eq(index - row);
            const domItemBefore = domItem.prev();
            
            if (domItemTop.length>0) {
                // Lấy vị trí của domItemTop
                const positionTop       = domItemTop.position();
                const positionBefore    = domItemBefore.position();
                // Thiết lập lại vị trí của domItem
                const newPositionTop    = positionTop.top + 16 + domItemTop.height();
                var newPositionleft     = positionBefore.left + 15 + domItemBefore.width();
                if(indexItem%row==0) newPositionleft = 0;
                domItem.css({
                    'position': 'absolute',
                    'top': newPositionTop + 'px',
                    'left': newPositionleft + 'px',
                    'width': domItemTop.width() + 'px',
                });
            }
        }
        function calculateColumns() {
            // Lấy chiều rộng của .freeWallpaperBox (bao gồm cả margin và padding)
            const boxWidth = $('.freeWallpaperBox').outerWidth();
            // Lấy kích thước mỗi cột trong grid
            const columnSize = $('.freeWallpaperBox').css('grid-template-columns').match(/\d+px/g);
            // Tính toán số cột hiển thị
            let totalColumns = 0;
            columnSize.forEach(size => {
                if (parseInt(size) <= boxWidth) {
                totalColumns++;
                }
            });
            return totalColumns;
        }
        /* loadmore wallpaper */
        var isFirstLoad = true; // Khai báo một biến để theo dõi xem đã load lần đầu tiên chưa
        function loadFreeWallpaperMore(requestLoad = 20){
            var boxCategory         = $('.freeWallpaperBox');
            const total             = $('#total').val();
            const loaded            = $('#loaded').val();
            const distanceLoad  = $('#topLoad').val();
            
            if($(window).scrollTop() + 1500 > distanceLoad&&boxCategory.length&&!boxCategory.hasClass('loading')) {
                if(isFirstLoad){
                    loadFreeWallpaperMoreToController(requestLoad);
                }else {
                    if(parseInt(total)>parseInt(loaded)){
                        loadFreeWallpaperMoreToController(requestLoad);
                    }
                }
            }
        }
        function loadFreeWallpaperMoreToController(requestLoad){
            /* Lấy chuỗi query parameters từ URL */
            var queryString = window.location.search;
            var urlParams = new URLSearchParams(queryString);
            var params = {};
            for (const [key, value] of urlParams) {
                params[key] = value;
            }
            /* lấy thông tin của input */
            var boxCategory         = $('.freeWallpaperBox');
            const total             = $('#total').val();
            const loaded            = $('#loaded').val();
            /* thêm class để đánh dấu đăng load => không load nữa */
            boxCategory.addClass('loading');
            /* lấy dữ liệu */
            params.total            = total;
            params.loaded           = loaded;
            params.array_category_info_id = $('#arrayIdCategory').val();
            params.request_load     = requestLoad;
            params.idNot            = $('#idNot').val();
            $.ajax({
                url         : '{{ route("main.category.loadmoreFreeWallpapers") }}',
                type        : 'get',
                dataType    : 'json',
                data        : params,
                success     : function(response){
                    setTimeout(function(){
                        lazyload();
                    }, 0);
                    /* xóa bỏ class để thể hiện đã load xong */
                    boxCategory.removeClass('loading');
                    /* append dữ liệu */
                    $('#loaded').val(response.loaded);
                    $('#total').val(response.total);
                    if(response.content!='') {
                        boxCategory.append(response.content);
                    }
                    // Kiểm tra khi tất cả các phần tử đã được load xong
                    waitForImagesLoaded(boxCategory, function () {
                        setViewAllImage();
                    });
                    // ngân click chuột phải các ảnh được load
                    preventClickImgAndEffectDownload();
                    /* thêm thông báo nếu empty */
                    if(boxCategory.children().length==0) boxCategory.html('<div>Không có kết quả phù hợp!');
                }
            });
            // Đặt isFirstLoad thành false sau lần đầu load
            isFirstLoad = false;
        }
        function preventClickImgAndEffectDownload(){
            $("img").on("contextmenu", function (e) {
                e.preventDefault();
                console.log("Chức năng chuột phải đã bị vô hiệu hóa cho ảnh này");
            });
            // Xử lý sự kiện click trên .action_item ẩn box khi click download
            $('.freeWallpaperBox_item .download').on('click', function (e) {
                e.stopPropagation(); // Ngăn chặn sự kiện click từ lan tỏa lên các phần tử cha

                // Tìm phần tử .freeWallpaperBox_item_box trong phần tử cha của .action_item và thêm style display: none; với !important
                $(this).closest('.freeWallpaperBox_item').find('.freeWallpaperBox_item_box').attr('style', 'display: none !important');
            });
            $('.freeWallpaperBox_item').hover(
                function () {
                    // Khi di chuột vào
                    $(this).find('.freeWallpaperBox_item_box').css('display', 'flex !important');
                },
                function () {
                    // Khi di chuột ra
                    $(this).find('.freeWallpaperBox_item_box').css('display', 'none');
                }
            );
        }
        // Hàm kiểm tra khi tất cả các hình ảnh trong boxCategory đã được load xong
        function waitForImagesLoaded(boxCategory, callback) {
            var images = boxCategory.find('img');
            var imagesToLoad = images.length;

            if (imagesToLoad === 0) {
                callback();
            }

            images.on('load', function () {
                imagesToLoad--;
                if (imagesToLoad === 0) {
                    callback();
                }
            }).each(function () {
                // Trigger sự kiện load để xử lý trường hợp ảnh đã được cache
                if (this.complete || this.complete === undefined) {
                    var src = this.src;
                    // Thiết lập src vào không gì để tránh lỗi caching
                    this.src = '#';
                    this.src = src;
                }
            });
        }
        function loadOneFreeWallpaper(idFreeWallpaper, idWrite){
            $.ajax({
                url         : '{{ route("ajax.loadOneFreeWallpaper") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    free_wallpaper_info_id : idFreeWallpaper
                },
                success     : function(response){
                    $('#'+idWrite).html(response);
                    lazyload();
                }
            });
        }
    </script>
@endpush