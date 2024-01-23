<?php
return [
    'left-menu-admin'   => [
        [
            'name'      => 'Quản lí đơn',
            'route'     => 'admin.order.list',
            'icon'      => '<i class="fa-regular fa-file-lines"></i>'
        ],
        [
            'name'      => 'Điện thoại',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-box-open"></i>',
            'child'     => [
                [
                    'name'  => '1. Wallpaper',
                    'route' => 'admin.wallpaper.list',
                    'icon'  => '<i data-feather=\'circle\'></i>',
                ],
                [
                    'name'  => '2. Sản phẩm',
                    'route' => 'admin.product.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '3. Chủ đề',
                    'route' => 'admin.category.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                // [
                //     'name'  => '4. Phong cách',
                //     'route' => 'admin.style.list',
                //     'icon'  => '<i data-feather=\'circle\'></i>'
                // ],
                // [
                //     'name'  => '5. Sự kiện',
                //     'route' => 'admin.event.list',
                //     'icon'  => '<i data-feather=\'circle\'></i>'
                // ],
                [
                    'name'  => '4. Miễn phí',
                    'route' => 'admin.freeWallpaper.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ]
            ]
        ],
        [
            'name'      => 'Quản lí trang',
            'route'     => 'admin.page.list',
            'icon'      => '<i class="fa-regular fa-file-lines"></i>',
            // 'child'     => [
            //     [
            //         'name'  => '1. Danh sách',
            //         'route' => '',
            //         'icon'  => '<i data-feather=\'circle\'></i>'
            //     ]
            // ]
        ],
        [
            'name'      => 'Quản lí Blog',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-blog"></i>',
            'child'     => [
                [
                    'name'  => '1. Tin tức',
                    'route' => 'admin.blog.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '1. Chuyên mục',
                    'route' => 'admin.categoryBlog.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ]
            ]
        ],
        [
            'name'      => 'Quản lí ảnh',
            'route'     => 'admin.image.list',
            'icon'      => '<i class="fa-regular fa-images"></i>',
            // 'child'     => [
            //     [
            //         'name'  => '1. Danh sách',
            //         'route' => 'admin.category.list',
            //         'icon'  => '<i data-feather=\'circle\'></i>'
            //     ]
            // ]
        ],
        [
            'name'      => 'Cài đặt',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-gear"></i>',
            'child'     => [
                [
                    'name'  => '1. Giao diện',
                    'route' => 'admin.theme.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. Slider home',
                    'route' => 'admin.setting.slider',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ]
            ]
        ],
        [
            'name'      => 'Công cụ SEO',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-screwdriver-wrench"></i>',
            'child'     => [
                [
                    'name'  => '4. Redirect 301',
                    'route' => 'admin.redirect.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ]
            ]
        ],
        [
            'name'      => 'Xóa cache',
            'route'     => 'admin.cache.clearCache',
            'icon'      => '<i class="fa-sharp fa-solid fa-xmark"></i>'
        ],
    ]
];