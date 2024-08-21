@if(!empty($item))
    <tr id="oneItem-{{ $item->id }}">
        <td>{{ $no }}</td>
        @php
            $urlImage = config('image.default');
            if(!empty($item->image)) $urlImage = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->image);
        @endphp
        {{-- <td class="text-center"><img src="{!! $urlImage.'?v='.time() !!}" style="width:100%;aspect-ratio:800/533;object-fit:cover;font-family:'SVN-Gilroy Bold',sans-serif;" /></td> --}}
        <td>
            @php
                $countBox = !empty($item->jobAutoTranslate) ? $item->jobAutoTranslate->count() : 0;
            @endphp
            <div class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;">
                <img src="/storage/images/svg/icon_flag_{{ $item->language }}.png" style="width:35px;margin-right:5px;" /> {{ $item->title ?? null }} <span style="font-weight:normal;font-size:16px;color:#28c76f;">({{ $countBox }} box)</span>
            </div>
            <div class="oneLine">
                <strong>Tiêu đề SEO</strong>: {{ $item->seo_title ?? null }} (<strong>{{ !empty($item->seo_title) ? mb_strlen($item->seo_title) : 0 }}</strong>)
            </div>
            <div class="oneLine">
                <strong>Mô tả SEO</strong>: {{ $item->seo_description ?? null }} (<strong>{{ !empty($item->seo_description) ? mb_strlen($item->seo_description) : 0 }}</strong>)
            </div>
            <div class="oneLine">
                <strong>Đường dẫn</strong>: {{ $item->slug_full ?? null }}
            </div>
        </td>
        <td class="text-center">
            @foreach($item->jobAutoTranslate as $l)
                <div class="oneLine" {{ $loop->index>0 ? 'style="border-top:1px dashed #333;padding-top:5px;"' : '' }}>
                    {!! $l->status==1 ? '<span style="color:#28c76f;">Thành công</span>' : '<span>Đang chờ...</span>' !!}
                </div>
            @endforeach
        </td>
        <td>
            @foreach($item->jobAutoTranslateLinks as $l)
                <div class="oneLine" {{ $loop->index>0 ? 'style="border-top:1px dashed #333;padding-top:5px;"' : '' }}>{{ ($loop->index+1) }}. <a target="_blank" href="{{ $l->link_translate }}">{{ $l->link_translate }}</a></div>
            @endforeach
        </td>
        <td style="vertical-align:top;display:flex;font-size:0.95rem;">
            <div class="icon-wrapper iconAction">
                <a href="/{{ $item->slug_full ?? null }}" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <div>Xem</div>
                </a>
            </div>
            <div class="icon-wrapper iconAction">
                <a href="#" onclick="redirectEdit({{ $item->id }}, '{{ $item->language }}');">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    <div>Sửa</div>
                </a>
            </div>
            <div class="icon-wrapper iconAction">
                <a href="#" onClick="reRequestTranslate({{ $item->id }}, '{{ $item->language }}');">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    <div>Lại</div>
                </a>
            </div>
            <div class="icon-wrapper iconAction">
                <div class="actionDelete" onclick="deleteItem({{ $item->id }});">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-square">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                    </svg>
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