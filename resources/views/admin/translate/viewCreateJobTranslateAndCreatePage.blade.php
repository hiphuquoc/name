@extends('layouts.admin')
@section('content')

<div class="titlePage">Chức năng tự động tạo trang đa ngôn ngữ</div>

<!-- MESSAGE -->
@include('admin.template.messageAction')

<!-- ===== START: SEARCH FORM ===== -->
<form id="formSearch" method="post" action="{{ route('admin.translate.createJobTranslateAndCreatePageAjax') }}">
@csrf
    <div class="searchBox">
        <div class="searchBox_item" style="max-width:unset;">
            <input type="text" class="form-control" name="url_vi" placeholder="Url bản Tiếng Việt của trang cần tạo" required />
        </div>
        <div class="searchBox_item" style="max-width:130px;">
            <button class="btn btn-primary waves-effect" id="button-addon2" type="submit" style="width:100%;">Thực hiện</button>
        </div>
    </div>
</form>
<!-- ===== END: SEARCH FORM ===== -->
    
@endsection