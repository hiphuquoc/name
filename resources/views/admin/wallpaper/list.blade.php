@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh sách ảnh</div>
<!-- ===== START: SEARCH FORM ===== -->

<div class="searchBox">
    <div class="searchBox_item">
        <form id="formSearch" method="get" action="{{ route('admin.wallpaper.list') }}">
            <div class="input-group">
                <input type="text" class="form-control" name="search_name" placeholder="Tìm theo tên" value="{{ $params['search_name'] ?? null }}">
                <button class="btn btn-primary waves-effect" id="button-addon2" type="submit" aria-label="Tìm">Tìm</button>
            </div>
        </form>
    </div>
    <div class="searchBox_item">
        <button class="btn btn-primary waves-effect" data-bs-toggle="modal" data-bs-target="#modalFormWallpaper"  id="button-addon2" aria-label="Tải lên" onClick="loadModalUploadAndEdit();">Tải lên</button>
    </div>
    <div class="searchBox_item" style="margin-left:auto;text-align:right;">
        @php
            $xhtmlSettingView   = \App\Helpers\Setting::settingView('viewWallpaperInfo', [20, 50, 100, 200, 500], $viewPerPage, $list->total());
            echo $xhtmlSettingView;
        @endphp
    </div>
</div>

<!-- ===== END: SEARCH FORM ===== -->
{{-- <div id="js_uploadImage_idWrite" class="imageBox" style="padding-bottom:2rem;">
    @if(!empty($list))
    @foreach($list as $item)
        @include('admin.wallpaper.oneRow', compact('item'))
    @endforeach
    @endif
</div> --}}
<div id="js_uploadAndChangeWallpaperWithSource_idWrite" class="wallpaperBox" style="padding-bottom:2rem;">
    @if(!empty($list))
        @foreach($list as $item)
            <div id="js_deleteWallpaperAndSource_{{ $item->id }}" class="wallpaperBox_item">
                @include('admin.wallpaper.oneRow', compact('item'))
            </div>
        @endforeach
    @endif
</div>

<!-- ===== START: MODAL ===== -->
<form id="formWallpaperWithSource" method="post" enctype="multipart/form-data">
@csrf
    <div class="modal modal-top fade" id="modalFormWallpaper" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog" style="max-width:1000px;">
            <div id="js_loadModalUploadAndEdit_box">
                <!-- load Ajax -->
            </div>
        </div>
    </div>
</form>
<!-- ===== END:: MODAL ===== -->
    
@endsection
@push('scriptCustom')
    <!-- BEGIN: SLICK -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <!-- END: SLICK -->
    <script type="text/javascript">

        function loadOneRow(idWallpaper){
            const idBox         = 'js_deleteWallpaperAndSource_'+idWallpaper;
            var boxWallpaper    = $('#'+idBox);
            const heightBox     = boxWallpaper.outerHeight();
            addLoading(idBox, heightBox);
            $.ajax({
                url: "{{ route('admin.wallpaper.loadOneRow') }}",
                type: "post",
                dataType: "html",
                data: {
                    id : idWallpaper
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            }).done(function (response) {
                boxWallpaper.html(response);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
            });
        }

        function readImageWhenChoose(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    const parentBox = $(input).parent();
                    parentBox.css({
                        'background' : "url('"+e.target.result+"') no-repeat",
                        'background-size' : '100% 100%'
                    });
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        function addFormUploadSource(input) {
            if (input.files && input.files.length > 0) {
                var dataId = [];
                for (let i = 0; i < input.files.length; ++i) {
                    dataId.push(i);
                }
                /* Gửi dataId vào ajax để tạo form */
                $.ajax({
                    url: "{{ route('admin.wallpaper.loadFormUploadSourceAndWallpaper') }}",
                    type: "post",
                    dataType: "html",
                    data: {
                        data_id: dataId
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                }).done(function (response) {
                    /* Append form vào #js_addFormUploadSource_box */
                    $('#js_addFormUploadSource_box').html(response);  
                    /* Refresh lại slick */
                    refreshSlick('formWallpaperBox_gallery');
                    /* Tách input multiple ra và điền giá trị vào từng input wallpaper */
                    for (let i = 0; i < dataId.length; ++i) {
                        let boxSource   = $('#js_addFormUploadSource_wallpaper_'+dataId[i]);
                        let reader      = new FileReader();
                        reader.onload   = function (e) {
                            /* Sử dụng .css() để đặt background image */
                            boxSource.css({
                                'background'        : "url('" + e.target.result + "')",
                                'background-size'   : '100% 100%'
                            });
                        };
                        reader.readAsDataURL(input.files[i]); // Đọc từng tệp ảnh riêng lẻ
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Ajax request failed: " + textStatus, errorThrown);
                });

            }
        }

        function uploadAndChangeWallpaperWithSource(idWallpaper = '') {
            const tmp          = validateForm('formWallpaperWithSource');
            if(tmp==''){
                /* bật loading */
                addLoadingModal();
                /* lấy dữ liệu form */
                const formData = new FormData($('#formWallpaperWithSource')[0]); // Sử dụng [0] để lấy ra DOM element thay vì jQuery object
                /* upload (create) */ 
                if(idWallpaper==''){
                    $.ajax({
                        url: "{{ route('admin.wallpaper.uploadWallpaperWithSource') }}",
                        type: "post",
                        dataType: 'json',
                        data: formData,
                        processData: false, // Không xử lý dữ liệu gửi đi
                        contentType: false, // Không thiết lập header Content-Type
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    }).done(function (response) {
                        /* tắt loading */
                        addLoadingModal();
                        /* prepend thêm box mới upload vào */
                        $.each(response, function(index, item) {
                            var newDiv = $("<div>", {
                                id: "js_deleteWallpaperAndSource_" + item.id,
                                class: "wallpaperBox_item",
                                html: item.content
                            });
                            // Thêm div mới vào đầu của container
                            newDiv.prependTo("#js_uploadAndChangeWallpaperWithSource_idWrite");
                        });
                        /* tắt modal */
                        $('#modalFormWallpaper').modal('hide');
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        console.error("Ajax request failed: " + textStatus, errorThrown);
                    });
                }else {
                    /* truyền thêm wallpaper_id */
                    formData.append('wallpaper_id', idWallpaper);
                    $.ajax({
                        url: "{{ route('admin.wallpaper.changeWallpaperWithSource') }}",
                        type: "post",
                        data: formData,
                        processData: false, // Không xử lý dữ liệu gửi đi
                        contentType: false, // Không thiết lập header Content-Type
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    }).done(function (response) {
                        /* tắt loading */
                        addLoadingModal();
                        /* load lại box */
                        loadOneRow(idWallpaper);
                        /* tắt modal */
                        $('#modalFormWallpaper').modal('hide');
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        console.error("Ajax request failed: " + textStatus, errorThrown);
                    });
                }   
                /* tải lại source => dùng cho loadOneRow */
                setTimeout(function(){
                    loadImageFromGoogleCloud();
                }, 300);             
            }else {
                /* có 1 vài trường required bị bỏ trống */
                let messageError        = 'Không được bỏ trống trường ';
                tmp.forEach(function(value, index, array){
                    switch (true) {
                        case /^sources\[\d+\]$/.test(value):
                            var index = parseInt(value.match(/\d+/)[0]) + 1;
                            messageError += '<strong>Ảnh gốc ' + index + '</strong>';
                            break;
                        case value === 'wallpapers[]':
                            messageError += '<strong>Wallpaper</strong>';
                            break;
                        case value === 'file_name':
                            messageError += '<strong>Đường dẫn ảnh</strong>';
                            break;
                        case value === 'name':
                            messageError += '<strong>Alt wallpaper</strong>';
                            break;
                        default:
                            break;
                    }
                    if(index!=parseInt(tmp.length-1)) messageError += ', ';
                })
                $('#js_validateFormModalHotelContact_message').css('display', 'block').html(messageError);
            }
        }

        function validateForm(idForm) {
            let error = [];
            $('#' + idForm).find(':input[required]').each(function () {
                if ($(this).val() === '') {
                    error.push($(this).attr('name'));
                }
            });
            return error;
        }

        function addLoadingModal() {
            const displayLoading = $('#js_addLoadingModal').css('display');
            if(displayLoading=='none'){
                $('#js_addLoadingModal').css('display', 'flex');
            }else {
                $('#js_addLoadingModal').css('display', 'none');
            }
        }

        function loadModalUploadAndEdit(idWallpaper = ''){
            $.ajax({
                url         : "{{ route('admin.wallpaper.loadModalUploadAndEdit') }}",
                type        : "post",
                dataType    : "html",
                data        : { 
                    '_token'    : '{{ csrf_token() }}', 
                    wallpaper_id : idWallpaper
                }
            }).done(function(data){
                $('#js_loadModalUploadAndEdit_box').html(data);
                // console.log($('#modalFormWallpaper').html());
            });
        }

        function deleteWallpaperAndSource(idBox, idWallpaper){
            const heightBox = $('#'+idBox).outerHeight();
            addLoading(idBox, heightBox);
            $.ajax({
                url         : "{{ route('admin.wallpaper.deleteWallpaperAndSource') }}",
                type        : "post",
                dataType    : "json",
                data        : { 
                    '_token'        : '{{ csrf_token() }}', 
                    id  : idWallpaper
                }
            }).done(function(data){
                setTimeout(() => {
                    if(data==true) $('#'+idBox).hide();
                }, 500)
            });
        }

        function addLoading(idBox, heightBox = 300){
            const htmlLoadding  = '<div style="display:flex;align-items:center;justify-content:center;height:'+heightBox+'px;width:100%;"><div class="spinner-grow text-primary me-1" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            $('#'+idBox).html(htmlLoadding);
        }

    </script>
@endpush