@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh sách yêu cầu</div>

@include('admin.product.search', compact('list', 'categories'))

<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width:900px;">
            <thead>
                <tr>
                    <th style="width:60px;"></th>
                    <th class="text-center">Thông tin</th>
                    <th class="text-center" width="800px">Wallpapers</th>
                    <th class="text-center" width="60px">-</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                        <tr id="item_{{ $item->id }}">
                            <td class="text-center">{{ ($loop->index + 1) }}</td>
                            <td style="display:flex;flex-direction:column;">
                                
                                <div class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:0.5rem;">
                                    {{ $item->name ?? $item->seo->title ?? null }} (<span style="font-weight:bold;color:#E74C3C;">{{ $item->code }}</span>)
                                </div>
                                <div class="oneLine">
                                    <strong>Tiêu đề Seo:</strong> {{ $item->seo->seo_title }}
                                </div>
                                <div class="oneLine">
                                    <strong>Mô tả Seo:</strong> {{ $item->seo->seo_description }}
                                </div>
                                <div class="oneLine">
                                    <strong>Dường dẫn tĩnh:</strong> {{ $item->seo->slug_full }}
                                </div>
                                <div class="oneLine" style="margin-top:1rem;">
                                    @include('admin.template.languageBox', [
                                        'item' => $item,
                                        'routeName' => 'admin.product.view',
                                    ])
                                </div>
                                <div class="oneLine">
                                    <strong>Giá trọn bộ:</strong> <span style="color:red;font-weight:bold;font-size:1.3rem;">{{ $item->price }}</span>
                                </div>
                                
                                @if(!empty($item->categories)&&$item->categories->isNotEmpty())
                                    <div class="oneLine">
                                        @php
                                            $xhtmlCategory      = null;
                                            foreach($item->categories as $category){
                                                if($category->infoCategory->seo->type=='category_info'){
                                                    $xhtmlCategory  .= '<div class="badge bg-primary" style="margin-left:0.25rem;">'.$category->infoCategory->seo->title.'</div>';
                                                }
                                            }
                                        @endphp 
                                        <strong>Chủ đề:</strong> {!! $xhtmlCategory !!}
                                    </div>
                                    <div class="oneLine">
                                        @php
                                            $xhtmlCategory      = null;
                                            foreach($item->categories as $category){
                                                if($category->infoCategory->seo->type=='style_info'){
                                                    $xhtmlCategory  .= '<div class="badge bg-primary" style="margin-left:0.25rem;">'.$category->infoCategory->seo->title.'</div>';
                                                }
                                            }
                                        @endphp 
                                        <strong>Phong cách:</strong> {!! $xhtmlCategory !!}
                                    </div>
                                    <div class="oneLine">
                                        @php
                                            $xhtmlCategory      = null;
                                            foreach($item->categories as $category){
                                                if($category->infoCategory->seo->type=='event_info'){
                                                    $xhtmlCategory  .= '<div class="badge bg-primary" style="margin-left:0.25rem;">'.$category->infoCategory->seo->title.'</div>';
                                                }
                                            }
                                        @endphp 
                                        <strong>Sự kiện:</strong> {!! $xhtmlCategory !!}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="priceProductBox">
                                    @foreach($item->prices as $price)
                                        <div class="priceProductBox_item">
                                            <div class="priceProductBox_item_image">
                                                @php
                                                    /* lấy ảnh Small */
                                                    $imageSmall = config('image.default');
                                                    if(!empty($price->wallpapers[0]->infoWallpaper->file_cloud_wallpaper)) $imageSmall  = \App\Helpers\Image::getUrlImageSmallByUrlImage($price->wallpapers[0]->infoWallpaper->file_cloud_wallpaper);
                                                @endphp     
                                                <img src="{{ $imageSmall }}" />
                                                @if($price->wallpapers->count()>1)
                                                    <div class="priceProductBox_item_image_count">
                                                        {{ $price->wallpapers->count() }}<i class="fa-regular fa-image"></i>
                                                    </div>
                                                @endif
                                                <div class="priceProductBox_item_image_price">
                                                    {{ number_format($price->price) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td style="vertical-align:top;display:flex;font-size:0.95rem;">
                                <div class="icon-wrapper iconAction">
                                    <a href="/{{ $item->seo->slug_full }}" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <div>Xem</div>
                                    </a>
                                </div>
                                <div class="icon-wrapper iconAction">
                                    <a href="{{ route('admin.product.view', ['id' => $item->id, 'language' => 'vi']) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        <div>Sửa</div>
                                    </a>
                                </div>
                                <div class="icon-wrapper iconAction">
                                    <a href="{{ route('admin.product.view', ['id' => $item->id, 'type' => 'copy', 'language' => 'vi']) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        <div>Chép</div>
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
                    @endforeach
                @else
                    <tr><td colspan="5">Không có dữ liệu phù hợp!</td></tr>
                @endif
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    {{ !empty($list&&$list->isNotEmpty()) ? $list->appends(request()->query())->links('admin.template.paginate') : '' }}
</div>

<!-- Nút thêm -->
<a href="{{ route('admin.product.view') }}" class="addItemBox">
    <i class="fa-regular fa-plus"></i>
    <span>Thêm</span>
</a>
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">
        function deleteItem(id){
            if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
                $.ajax({
                    url         : "{{ route('admin.product.delete') }}",
                    type        : "GET",
                    dataType    : "html",
                    data        : { id : id }
                }).done(function(data){
                    if(data==true) $('#item_'+id).remove();
                });
            }
        }
    </script>
@endpush