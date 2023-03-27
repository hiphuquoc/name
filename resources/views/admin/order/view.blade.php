@extends('layouts.admin')
@section('content')

    {{-- <div class="titlePage">Danh sách yêu cầu</div>
    @include('admin.order.search')
    @include('admin.order.test', compact('list')) --}}

    @include('admin.order.contentView')
    
@endsection
@push('scripts-custom')
    <script type="text/javascript">
        function deleteItem(id){
            // if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
                $.ajax({
                    url         : "",
                    type        : "GET",
                    dataType    : "html",
                    data        : { id : id }
                }).done(function(data){
                    if(data==true) $('#tourLocation-'+id).remove();
                });
            // }
        }

        function submitForm(idForm){
            $('#'+idForm).submit();
        }
    </script>
@endpush