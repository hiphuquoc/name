@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh sách Danh mục</div>

@include('admin.categoryBlog.search', compact('list'))

<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width:900px;">
            <thead>
                <tr>
                    <th style="width:60px;"></th>
                    <th style="width:150px;">Ảnh</th>
                    <th class="text-center">Thông tin</th>
                    <th class="text-center" style="width:230px;">Khác</th>
                    <th class="text-center" width="60px">-</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                        @include('admin.categoryBlog.row', [
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
</div>

<!-- Nút thêm -->
<a href="{{ route('admin.categoryBlog.view') }}" class="addItemBox">
    <i class="fa-regular fa-plus"></i>
    <span>Thêm</span>
</a>
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">
        // function deleteItem(id, idRow){
        //     if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
        //         $.ajax({
        //             url         : '',
        //             type        : 'GET',
        //             dataType    : 'html',
        //             data        : { id : id }
        //         }).done(function(data){
        //             if(data==true) $('#'+idRow).remove();
        //         });
        //     }
        // }
        function submitForm(idForm){
            $('#'+idForm).submit();
        }
    </script>
@endpush