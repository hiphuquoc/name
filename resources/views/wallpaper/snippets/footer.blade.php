<div class="copyright">
    <div class="container">
        @if(empty($language)||$language=='vi')
            Bản quyền <a href="{{ env('APP_URL') }}" aria-label="Trang hình nền hàng đầu - Name.com.vn">Name.com.vn</a> - Thiết kế và phát triển bởi Phạm Văn Phú!
        @else 
            Copyright <a href="{{ env('APP_URL') }}/en" aria-label="Top wallpaper site - Name.com.vn">Name.com.vn</a> - Designed and developed by Pham Van Phu!
        @endif
    </div>
</div>