@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh sách Redirect</div>
<!-- ===== START: SEARCH FORM ===== -->
<form id="formSearch" method="get" action="{{ route('admin.redirect.create') }}">
<div class="searchBox">
    <div class="searchBox_item">
        <input type="text" class="form-control" name="old_url" placeholder="Đường dẫn cũ" value="{{ $params['old_url'] ?? null }}">
    </div>
    <div class="searchBox_item">
        <input type="text" class="form-control" name="new_url" placeholder="Đường dẫn mới" value="{{ $params['new_url'] ?? null }}">
    </div>
    <div class="searchBox_item">
        <button class="btn btn-primary waves-effect" id="button-addon2" type="submit">Thêm mới</button>
    </div>
</div>
</form>
<!-- ===== END: SEARCH FORM ===== -->

<!-- MESSAGE -->
@include('admin.template.messageAction')

<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center" style="width:70px;"></th>
                    <th class="text-center">Đường dẫn cũ</th>
                    <th class="text-center">Đường dẫn mới</th>
                    <th class="text-center" style="width:60px;">-</th>
                </tr>
            </thead>
            <tbody>
                @if($list->isNotEmpty())
                    @foreach($list as $item)
                        @include('admin.redirect.oneRow', ['no' => $loop->index + 1, 'item' => $item])
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">Không tìm thấy dữ liệu phù hợp!</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">

        function deleteItem(id){
            $.ajax({
                url         : "{{ route('admin.redirect.delete') }}",
                type        : "GET",
                dataType    : "html",
                data        : { id : id }
            }).done(function(data){
                if(data==true) $('#redirect_'+id).remove();
            });
        }
    </script>
@endpush