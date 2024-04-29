@extends('layouts.admin')
@section('content')

<div class="titlePage">Chức năng Copy Sản Phẩm Hàng Loạt</div>

<!-- MESSAGE -->
@include('admin.template.messageAction')

<!-- ===== START: SEARCH FORM ===== -->
<form id="formSearch" method="get" action="{{ route('admin.toolCopyProduct.create') }}">
    <div class="searchBox">
        <div class="searchBox_item" style="max-width:unset;">
            <input type="text" class="form-control" name="product_source" placeholder="Slug sản phẩm gốc" value="{{ $params['product_source'] ?? null }}" required />
        </div>
        <div class="searchBox_item" style="max-width:unset;">
            <input type="text" class="form-control" name="product_copy" placeholder="Slug tìm kiếm sản phẩm copy" value="{{ $params['product_copy'] ?? null }}" required />
        </div>
        <div class="searchBox_item" style="max-width:130px;">
            <button class="btn btn-primary waves-effect" id="button-addon2" type="submit" style="width:100%;">Thực hiện</button>
        </div>
    </div>
</form>
<!-- ===== END: SEARCH FORM ===== -->

{{-- <div class="card">
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
                        @include('admin.toolCopyProduct.oneRow', ['no' => $loop->index + 1, 'item' => $item])
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">Không tìm thấy dữ liệu phù hợp!</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div> --}}
    
@endsection