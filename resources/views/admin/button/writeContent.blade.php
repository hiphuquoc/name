<div class="actionBox_item maxLine_1" onClick="createJobWriteContent({{ $itemSeo->id ?? 0 }});">
    <i class="fa-solid fa-robot"></i>Viết nội dung (chạy ngầm)
</div>
@push('scriptCustom')

    <script type="text/javascript">
        function createJobWriteContent(idSeo) {
            var htmlBody = `
                <div>Thao tác này sẽ tiến hành <span style="color:red;font-weight:bold">*xóa nội dung bảng VI</span> của trang này và viết lại (chạy ngầm).<br/>Bạn có chắc muốn thực hiện?</div>`;

            Swal.fire({
                title: 'Xác nhận thao tác',
                html: htmlBody,
                preConfirm: () => {
                    
                },
                showLoaderOnConfirm: true,
                confirmButtonText: 'Xác nhận'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.translate.createJobWriteContent') }}",
                        type: "post",
                        dataType: "json",
                        data: { 
                            '_token': '{{ csrf_token() }}',
                            seo_id : idSeo,
                        }
                    })
                    .done(function(response) {
                        // Hiển thị Toast từ response
                        createToast(response.toast_type, response.toast_title, response.toast_message);
                    })
                    .fail(function() {
                        // Hiển thị thông báo lỗi mặc định
                        createToast('error', 'Thất bại', '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.');
                    })
                }
            });
        }

    </script>

@endpush