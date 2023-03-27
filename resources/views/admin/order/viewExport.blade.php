@extends('layouts.admin')
@section('content')

<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        @include('main.order.confirm', compact('order'))
    </div>
</div>
    
@endsection
@push('scriptCustom')
    {{-- <script type="text/javascript">
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
    </script> --}}
@endpush