<!DOCTYPE html>
<html lang="vi">

<!-- === START:: Head === -->
<head>
    @include('admin.snippets.head')
    @stack('headCustom')
</head>
<!-- === END:: Head === -->

<!-- === START:: Body === -->
<body class="pace-done vertical-layout vertical-menu-modern navbar-floating footer-static menu-expanded" data-open="click" data-menu="vertical-menu-modern" data-col>
    <div id="js_fullLoading_blur">
        <!-- === START:: Header === -->
        @include('admin.snippets.menu')
        <!-- === END:: Header === -->

        <!-- === START:: Content === -->
        <div class="app-content content">
            <div class="content-overlay"></div>
            @yield('content')
        </div>
    </div>

    <!-- === START:: Footer === -->
    {{-- @include('snippets.footer') --}}
    <!-- === END:: Footer === -->

    @stack('modal')
    
    <!-- === START:: Scripts Default === -->
    @include('admin.snippets.scriptDefault')
    <!-- === END:: Scripts Default === -->

    <!-- === START:: Scripts Custom === -->
    @stack('scriptCustom')
    <!-- === END:: Scripts Custom === -->
</body>
<!-- === END:: Body === -->

</html>