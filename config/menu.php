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
                    'name'  => '1. Kho trả phí',
                    'route' => 'admin.wallpaper.list',
                    'icon'  => '<i data-feather=\'circle\'></i>',
                ],
                [
                    'name'  => '2. Kho miễn phí',
                    'route' => 'admin.freeWallpaper.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '3. Sản phẩm',
                    'route' => 'admin.product.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '4. Chủ đề',
                    'route' => 'admin.category.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '5. Tag',
                    'route' => 'admin.tag.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '6. Seo từng ảnh',
                    'route' => 'admin.seoFreeWallpaper.list',
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
                    'name'  => '1. Redirect 301',
                    'route' => 'admin.redirect.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. Copy Đa SP',
                    'route' => 'admin.toolCopyProduct.view',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
            ]
        ],
        [
            'name'      => 'Công nghệ AI',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-robot"></i>',
            'child'     => [
                [
                    'name'  => '1. Prompt',
                    'route' => 'admin.prompt.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. API AI',
                    'route' => 'admin.apiai.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
            ]
        ],
    ]
];