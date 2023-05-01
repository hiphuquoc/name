@extends('layouts.wallpaper')
@push('headCustom')
@section('content')
    <div style="overflow:hidden;">
        <div class="contentBox">
            <div class="container">
                <h1>Danh sách tải xuống</h1>
                <div class="pageCartBox">
                    <div class="pageCartBox_left" style="width:100%;">
                    
                        @include('wallpaper.account.item')

                    </div>
                    <div class="pageCartBox_right">
                        {{-- <div id="js_scrollMenu" class="cartSectionBox">
                            <div class="actionPageConfirm">
                                <div class="actionPageConfirm_item">
                                    <div class="actionPageConfirm_item_icon">
                                        <img src="{{ Storage::url('images/svg/download-success.svg') }}" />
                                    </div>
                                    <div class="actionPageConfirm_item_text">
                                        Tải tất cả (.zip)
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('modal')
    {{-- <div id="js_toggleWaiting_box" class="waitingBox">
        <div class="waitingBox_box">

            <div id="loading_1">
                <img class="loading_1" src="{{ config('image.loading_main_css') }}" alt="loading cart" title="loading cart" />
            </div>
            <div class="waitingBox_box_text">Đang tải ảnh...</div>

        </div>
        <div class="waitingBox_bg"></div>
    </div> --}}
@endpush
@push('bottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">

        $(window).ready(function(){
            
        });

        
    </script>
@endpush