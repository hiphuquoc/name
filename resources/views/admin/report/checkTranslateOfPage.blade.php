@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh Sách Gợi Ý Sửa Lỗi</div>
@include('admin.report.searchCheckTranslateOfPage', compact('list'))
<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width:900px;">
            <thead>
                <tr>
                    <th style="width:60px;"></th>
                    <th class="text-center" style="width:180px;">Thông tin</th>
                    <th class="text-center">Bản dịch cũ</th>
                    <th class="text-center">Gợi ý sửa</th>
                    <th class="text-center" width="200px">-</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                        @include('admin.report.rowCheckTranslateOfPage', [
                            'item'  => $item,
                            'no'    => $loop->index+1
                        ])
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

{{-- <!-- Nút thêm -->
<a href="{{ route('admin.category.view') }}" class="addItemBox">
    <i class="fa-regular fa-plus"></i>
    <span>Thêm</span>
</a> --}}
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">
        function deleteItem(id){
            if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
                $.ajax({
                    url         : "{{ route('admin.translate.delete') }}",
                    type        : "get",
                    dataType    : "html",
                    data        : { id : id }
                }).done(function(data){
                    if(data==true) {
                        $('#oneItem-'+id).remove();
                        $('#oneItemSub-'+id).remove();
                    }
                });
            }
        }
    </script>
@endpush