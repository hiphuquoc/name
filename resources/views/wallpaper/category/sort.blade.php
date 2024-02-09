@if(!empty($infoFreeWallpaper))
    <!-- search -->
    @include('wallpaper.template.searchMessage', compact('total', 'infoFreeWallpaper', 'language'))
@else 
    <!-- filter box -->
    <form id="formViewBy" action="{{ url()->current().'?'.http_build_query(request()->query()) }}" method="GET">
        @include('wallpaper.category.sortContent', [
            'language'          => $language ?? 'vi',
            'total'             => $total,
            'categories'        => $categories ?? null,
            'categoryChoose'    => $categoryChoose ?? null,
            'searchFeeling'     => $searchFeeling ?? null
        ])
    </form>
@endif

@pushonce('scriptCustom')
    <script type="text/javascript">
        $(document).ready(function () {
            showSortBoxFreeWallpaper();
        });

        function showSortBoxFreeWallpaper(){
            const id = "{{ $item->id ?? 0 }}";
            const total = "{{ $total ?? 0 }}";

            // Lấy chuỗi query parameters từ URL
            var queryString = window.location.search;
            
            // Tạo một đối tượng URLSearchParams từ chuỗi query parameters
            var urlParams = new URLSearchParams(queryString);

            // Lấy tất cả các tham số truyền qua URL
            var params = {};
            for (const [key, value] of urlParams) {
                params[key] = value;
            }

            // Thêm các giá trị id và total vào params
            params['id'] = id;
            params['total'] = total;

            $.ajax({
                url: "{{ route('ajax.showSortBoxFreeWallpaper') }}",
                type: 'get',
                dataType: 'html',
                data: params,
            }).done(function (response) {
                $('#formViewBy').html(response);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
            });
        }

        function toggleFilterAdvanced(idElement){
            $('#'+idElement).toggleClass('active');
        }

        function setViewBy(key){
            $.ajax({
                url         : '{{ route("ajax.setViewBy") }}',
                type        : 'get',
                dataType    : 'json',
                data        : {
                    key
                },
                success     : function(response){
                    location.reload();
                }
            });
        }

        function setSortBy(key){
            $.ajax({
                url         : '{{ route("ajax.setSortBy") }}',
                type        : 'get',
                dataType    : 'json',
                data        : {
                    key
                },
                success     : function(response){
                    location.reload();
                }
            });
        }

        // function setFeelingAndSubmit(element) {
        //     // Xác định input checkbox trong phần tử cha của element
        //     var checkbox = $(element).find('input[type="checkbox"]');

        //     // Toggle trạng thái checked của checkbox
        //     checkbox.prop('checked', !checkbox.prop('checked'));

        //     // Submit form
        //     $(element).closest('form').submit();
        // }

        function setFilter(element){
            var checkbox = $(element).find('input[type="radio"]');
            checkbox.prop('checked', true);
            $(element).closest('form').submit();
        }
    </script>
@endpushonce