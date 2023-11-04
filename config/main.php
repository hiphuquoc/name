<?php

return [
    'currency_unit'         => '<sup>đ</sup>',
    'currency_unit_en'      => ' US$',
    'exchange_rate'         => [
        'usd_to_vnd'    => 22450
    ],
    'author_name'           => 'Name.com.vn',
    'founder_name'          => 'Name.com.vn',
    'founder_address'       => '55 Cô Giang, Rạch Giá',
    'founding'              => '2023-03-30',
    'company_name'          => 'Name.com.vn',
    'hotline'               => '0968.6171.68',
    'email'                 => 'anhnendienthoai@gmail.com',
    'address'               => '55 Cô Giang, Rạch Giá',
    'company_description'   => 'Giới thiệu dịch vụ',
    // 'logo_750x460'          => 'public/images/upload/trang-diem-750.webp',
    'logo_main'             => 'images/upload/logo-type-manager-upload.webp',
    'contacts'          => [
        [
            'type'      => 'customer service',
            'phone'     => '0968617168'
        ],
        [
            'type'      => 'technical support',
            'phone'     => '0968617168'
        ],
        [
            'type'      => 'sales',
            'phone'     => '0968617168'
        ]
    ],
    'products'          => [
        [
            'type'      => 'Product',
            'product'   => 'Thương mại điện tử'
        ]
    ],
    'socials'           => [
        'https://facebook.com/name',
        'https://twitter.com/name',
        'https://pinterest.com/name',
        'https://youtube.com/name'
    ],
    'storage'   => [
        'contentPage'       => 'public/contents/pages/',
        'contentBlog'       => 'public/contents/blogs/',
        'contentCategory'   => 'public/contents/categories/',
        'contentStyle'      => 'public/contents/styles/',
        'enContentCategory' => 'public/contents/enCategories/',
        'enContentPage'     => 'public/contents/enPages/',
    ],
    'google_cloud_storage' => [
        'wallpapers'        => 'wallpapers/',
        'sources'           => 'sources/',
    ],
    'filter'    => [
        'price' => [
            [
                'name'  => 'Nhỏ hơn 100,000đ',
                'min'   => '0',
                'max'   => '100000'
            ],
            [
                'name'  => 'Từ 100,000đ - 200,000đ',
                'min'   => '100000',
                'max'   => '200000'
            ],
            [
                'name'  => 'Từ 200,000đ - 350,000đ',
                'min'   => '200000',
                'max'   => '350000'
            ],
            [
                'name'  => 'Từ 350,000đ - 500,000đ',
                'min'   => '350000',
                'max'   => '500000'
            ],
            [
                'name'  => 'Từ 500,000đ - 1,000,000đ',
                'min'   => '500000',
                'max'   => '1000000'
            ],
            [
                'name'  => 'Trên 1,000,000đ',
                'min'   => '1000000',
                'max'   => '9999999999999999999999'
            ]
        ]
    ],
    'view_by' => [
        'set'   => [
            'name'      => 'Từng bộ',
            'en_name'   => 'Per set'
        ],
        'wallpaper'   => [
            'name'      => 'Từng ảnh',
            'en_name'   => 'Per wallpaper'
        ]
    ],
    'cache'     => [
        'extension'     => 'html',
        'folderSave'    => 'public/caches/',
    ],
    'main.password_user_default' => 'hitourVN@mk123',
];