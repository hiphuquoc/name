@if(!empty($item))
    <tr id="oneItem-{{ $item->id }}">
        <td>{{ $no }}</td>

        <td>
            <div class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;color:#009e69;">
                {{ $item->title_vi ?? null }} 
            </div>
            <div class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;color:#009e69;">
                {{ $item->title_en ?? null }} 
            </div>
            <div class="oneLine">
                <strong>Ngôn ngữ</strong>: {{ $item->language ?? null }}
            </div>
            <div class="oneLine">
                <strong>Loại</strong>: {{ $item->type ?? null }}
            </div>
        </td>
        <td>
            @php
                $keySearch  = '';
                $tmp        = explode(' ', $item->title);
                $keySearch  = implode('+', $tmp);
                $translate  = '('.$item->title_google_translate_vi.' - '.$item->title_google_translate_en.')';
            @endphp 
            <a href="https://www.google.com/search?q={{ $keySearch }}" target="_blank" class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;color:#e67112;">
                {{ $item->title ?? null }} - {{ $translate }}
            </a>
            <div class="oneLine">
                {{ $item->seo_title ?? null }}
            </div>
            <div class="oneLine">
                {{ $item->seo_description ?? null }}
            </div>
        </td>
        <td>
            @php
                $keySearch  = '';
                $tmp        = explode(' ', $item->new_title);
                $keySearch  = implode('+', $tmp);
                $translate  = '('.$item->new_title_google_translate_vi.' - '.$item->new_title_google_translate_en.')';
            @endphp 
            <a href="https://www.google.com/search?q={{ $keySearch }}" target="_blank" class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;color:#009e69;">
                {{ $item->new_title ?? null }} - {{ $translate }}
            </a>
            <div class="oneLine">
                {{ $item->new_seo_title ?? null }}
            </div>
            <div class="oneLine">
                {{ $item->new_seo_description ?? null }}
            </div>
        </td>
        <td>
            <div class="actionBoxOfList">
                <div onclick="">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <div>Sửa</div>
                </div>
                <div class="actionCheck" onClick="">
                    <i class="fa-solid fa-repeat"></i>
                    <div>Lại</div>
                </div>
                <div class="actionDelete" onclick="deleteItem({{ $item->id }});">
                    <i class="fa-solid fa-trash"></i>
                    <div>Xóa</div>
                </div>
            </div>
        </td>
    </tr>
    <!-- nếu có phần tử con => viết tiếp -->
    @if(!empty($item->childs)&&$item->childs->isNotEmpty())
        @foreach($item->childs as $child)
            @include('admin.category.row', [
                'item'  => $child,
                'no'    => $no.'.'.($loop->index+1)
            ])
        @endforeach
    @endif
@endif

@pushonce('scriptCustom')
    <script type="text/javascript">
        function reRequestTranslate(idSeo, language){
            $.ajax({
                url         : "{{ route('admin.translate.reRequestTranslate') }}",
                type        : "post",
                dataType    : "html",
                data        : { 
                    '_token'        : '{{ csrf_token() }}', 
                    id_seo : idSeo,
                    language
                }
            }).done(function(data){
                $('#oneItem-'+idSeo).remove();
            });
        }
        function redirectEdit(idSeoByLanguage, language){
            $.ajax({
                url         : "{{ route('admin.translate.redirectEdit') }}",
                type        : "get",
                dataType    : "json", // Đảm bảo dataType là json
                data        : {
                    id_seo_by_language : idSeoByLanguage,
                    language,
                }
            }).done(function(response){
                // Mở tab mới với URL trả về từ server
                window.open(response.url, '_blank');
            });
        }
    </script>
@endpushonce