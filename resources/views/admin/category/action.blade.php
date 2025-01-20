<div class="actionBox">
    @if($language=='vi')

        <!-- nut viết toàn trang (trực quan) -->
        @include('admin.button.writeAll')

        <!-- tạo trang ngôn ngữ --> 
        @include('admin.button.translatePage')

        <!-- dịch content (những trang chưa có) -->
        @include('admin.button.translateContentWithOption')

    @else   

        <!-- dịch toàn trang (chạy ngầm) -->
        @include('admin.button.translateAll')

        <!-- dịch content trang này (chạy ngầm) -->
        @include('admin.button.translateContentThisLanguage')

    @endif

    <!-- copy sang trang con -->
    @include('admin.button.copyAllToPageChild')

    <!-- copy từ trang cha (dành cho trang gốc) -->
    @include('admin.button.copyAllFromPageParent')

</div>