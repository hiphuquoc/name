@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh Sách Prompt</div>

@include('admin.prompt.search', compact('list'))

<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width:900px;">
            <thead>
                <tr>
                    <th></th>
                    <th>Loại</th>
                    <th>Bảng</th>
                    <th>Cột</th>
                    <th width="120px">Công cụ</th>
                    <th width="120px">Phiên bản</th>
                    <th>Prompt</th>
                    <th>-</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                        <tr id="oneItem-{{ $item->id }}">
                            <td class="text-center">{{ ($loop->index + 1) }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->reference_table }}</td>
                            <td>{{ $item->reference_name }}</td>
                            <td>{{ $item->tool }}</td>
                            <td>{{ $item->version }}</td>
                            <td><div style="white-space: pre-line;">{{ $item->reference_prompt }}</div></td>
                            <td style="vertical-align:top;display:flex;font-size:0.95rem;">
                                <div class="icon-wrapper iconAction">
                                    <a href="{{ route('admin.prompt.view', ['id' => $item->id]) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        <div>Sửa</div>
                                    </a>
                                </div>
                                <div class="icon-wrapper iconAction">
                                    <a href="{{ route('admin.prompt.view', ['id' => $item->id, 'type' => 'copy']) }}">
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
<a href="{{ route('admin.prompt.view') }}" class="addItemBox">
    <i class="fa-regular fa-plus"></i>
    <span>Thêm</span>
</a>
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">
        function deleteItem(id){
            if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
                $.ajax({
                    url         : "{{ route('admin.prompt.delete') }}",
                    type        : "get",
                    dataType    : "html",
                    data        : { id }
                }).done(function(data){
                    if(data==true) $('#oneItem-'+id).remove();
                });
            }
        }
    </script>
@endpush