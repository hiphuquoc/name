@extends('layouts.admin')
@section('content')

    {{-- <div class="titlePage">Danh sách yêu cầu</div>
    @include('admin.product.search')
    @include('admin.product.test', compact('list')) --}}

    @include('admin.product.contentView')
    
@endsection