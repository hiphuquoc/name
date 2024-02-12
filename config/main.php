<?php

return [
    'currency_unit'         => '<sup>đ</sup>',
    'currency_unit_en'      => ' US$',
    'exchange_rate'         => [
        'usd_to_vnd'    => 22000
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
        'enContentCategory' => 'public/contents/enCategories/',

        'enContentPage'     => 'public/contents/enPages/'
    ],
    'google_cloud_storage' => [
        'default_domain'    => 'https://'.env('GOOGLE_CLOUD_STORAGE_BUCKET').'.storage.googleapis.com/',
        'wallpapers'        => 'wallpapers/',
        'sources'           => 'sources/',
        'freeWallpapers'    => 'freewallpapers/',
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
        [
            'icon'      => '<i class="fa-solid fa-gift"></i>',
            'name'      => 'Từng bộ',
            'en_name'   => 'Per set',
            'key'       => 'set'
        ],
        [
            'icon'      => '<i class="fa-regular fa-image"></i>',
            'name'      => 'Từng ảnh',
            'en_name'   => 'Per wallpaper',
            'key'       => 'wallpaper'
        ]
    ],
    'cache'     => [
        'extension'     => 'html',
        'folderSave'    => 'public/caches/',
    ],
    'main.password_user_default' => 'hitourVN@mk123',
    'message'   => [
        'vi'    => [
            'product_empty' => 'Không có hình nền phù hợp!'
        ],
        'en'    => [
            'product_empty' => 'No wallpapers matching your search!'
        ]
        ],
    'category_type' => [
        [
            'key' => 'category_info',
            'name' => 'Chủ đề',
            'en_name'   => 'Category'
        ],
        [
            'key' => 'style_info',
            'name' => 'Phong cách',
            'en_name'   => 'Style'
        ],
        [
            'key' => 'event_info',
            'name' => 'Sự kiện',
            'en_name'   => 'Event'
        ]
    ],
    'sort_type' => [
        [
            'icon'      => '<i class="fa-solid fa-star"></i>',
            'key'       => 'propose',
            'name'      => 'Đề xuất',
            'en_name'   => 'Propose'
        ],
        [
            'icon'      => '<i class="fa-solid fa-heart"></i>',
            'key'       => 'favourite',
            'name'      => 'Yêu thích',
            'en_name'   => 'Favourite'
        ],
        [
            'icon'      => '<i class="fa-solid fa-arrow-down"></i>',
            'key'       => 'new',
            'name'      => 'Mới nhất',
            'en_name'   => 'Latest'
        ],
        [
            'icon'      => '<i class="fa-solid fa-arrow-up"></i>',
            'key'       => 'old',
            'name'      => 'Cũ nhất',
            'en_name'   => 'Oldest'
        ]
    ],
    'feeling_type'  => [
        [
            'icon'          => 'storage/images/svg/icon-vomit-2.svg',
            'icon_unactive' => 'storage/images/svg/icon-vomit-2-unactive.svg',
            'key'           => 'vomit',
            'name'          => 'Ói',
            'en_name'       => 'Vomit'
        ],
        [
            'icon'      => 'storage/images/svg/icon-notLike-2.svg',
            'icon_unactive' => 'storage/images/svg/icon-notLike-2-unactive.svg',
            'key'       => 'notlike',
            'name'      => 'Không thích',
            'en_name'   => 'Not like'
        ],
        [
            'icon'      => 'storage/images/svg/icon-haha-2.svg',
            'icon_unactive' => 'storage/images/svg/icon-haha-2-unactive.svg',
            'key'       => 'haha',
            'name'      => 'Haha',
            'en_name'   => 'Haha'
        ],
        [
            'icon'      => 'storage/images/svg/icon-heart-2.svg',
            'icon_unactive' => 'storage/images/svg/icon-heart-2-unactive.svg',
            'key'       => 'heart',
            'name'      => 'Thả tim',
            'en_name'   => 'Heart'
        ]
    ],
    'auto_fill' => [
        'alt'   => [
            'vi'    => 'Hình nền điện thoại',
            'en'    => 'Phone wallpaper'
        ],
        'slug'  => [
            'vi'    => 'tag-hinh-nen-dien-thoai',
            'en'    => 'tag-phone-wallpaper'
        ]
    ],

];