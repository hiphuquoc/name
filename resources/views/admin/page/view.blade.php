@extends('layouts.admin')
@section('content')

    {{-- <div class="titlePage">Danh sách yêu cầu</div>
    @include('admin.page.search')
    @include('admin.page.test', compact('list')) --}}

    @include('admin.page.contentView')
    
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