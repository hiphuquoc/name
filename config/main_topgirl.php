<?php

return [
    'info'  => [
        'name'  => [
            'author_name'           => 'Name.com.vn',
            'founder_name'          => 'Name.com.vn',
            'founder_address'       => '55 Cô Giang, Rạch Giá',
            'founding'              => '2023-03-30',
            'company_name'          => 'Name.com.vn',
            'email'                 => 'anhnendienthoai@gmail.com',
            'company_description'   => 'Giới thiệu dịch vụ',
            'contacts'          =>  [
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
        ],
        'topgirl'   => [
            'author_name'           => 'topGirl.com.vn',
            'founder_name'          => 'topGirl.com.vn',
            'founder_address'       => 'Viet Nam',
            'founding'              => '2024-05-30',
            'company_name'          => 'topGirl.com.vn',
            'email'                 => 'topgirl.com.vn@gmail.com',
            'company_description'   => 'Giới thiệu dịch vụ',
            'contacts'          =>  [
                                        [
                                            'type'      => 'customer service',
                                            'phone'     => '0869112676'
                                        ],
                                        [
                                            'type'      => 'technical support',
                                            'phone'     => '0869112676'
                                        ],
                                        [
                                            'type'      => 'sales',
                                            'phone'     => '0869112676'
                                        ]
                                    ],
        ],
        'topanime'  => [
            'author_name'           => 'topAnime.com.vn',
            'founder_name'          => 'topAnime.com.vn',
            'founder_address'       => 'Viet Nam',
            'founding'              => '2024-07-30',
            'company_name'          => 'topAnime.com.vn',
            'email'                 => 'topanime.com.vn@gmail.com',
            'company_description'   => 'Giới thiệu dịch vụ',
            'contacts'          =>  [
                                        [
                                            'type'      => 'customer service',
                                            'phone'     => '0889999553'
                                        ],
                                        [
                                            'type'      => 'technical support',
                                            'phone'     => '0889999553'
                                        ],
                                        [
                                            'type'      => 'sales',
                                            'phone'     => '0889999553'
                                        ]
                                    ],
        ]
    ],
    // 'author_name'           => 'Name.com.vn',
    // 'founder_name'          => 'Name.com.vn',
    // 'founder_address'       => '55 Cô Giang, Rạch Giá',
    // 'founding'              => '2023-03-30',
    // 'company_name'          => 'Name.com.vn',
    // 'hotline'               => '0968.6171.68',
    // 'email'                 => 'anhnendienthoai@gmail.com',
    // 'address'               => '55 Cô Giang, Rạch Giá',
    // 'company_description'   => 'Giới thiệu dịch vụ',
    // 'logo_750x460'          => 'public/images/upload/trang-diem-750.webp',
    'logo_main'             => 'images/upload/logo-type-manager-upload.webp',
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
    'google_cloud_storage' => [
        'default_domain'    => 'https://'.env('GOOGLE_CLOUD_STORAGE_BUCKET').'.storage.googleapis.com/',
        // 'wallpapers'        => 'test/',
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
            'key'       => 'each_set'
        ],
        [
            'icon'      => '<i class="fa-regular fa-image"></i>',
            'key'       => 'each_image'
        ]
    ],
    'cache'     => [
        'extension'     => 'html',
        'folderSave'    => 'public/caches/',
    ],
    'main.password_user_default' => 'hitourVN@mk123',
    'category_type' => [
        [
            'key' => 'category_info',
            'key_filter_language'   => 'filter_by_themes',
            'name' => 'Chủ đề'
        ],
        [
            'key' => 'style_info',
            'key_filter_language'   => 'filter_by_styles',
            'name' => 'Phong cách'
        ],
        [
            'key' => 'event_info',
            'key_filter_language'   => 'filter_by_events',
            'name' => 'Sự kiện'
        ]
    ],
    'sort_type' => [
        [
            'icon'      => '<i class="fa-solid fa-star"></i>',
            'key'       => 'propose',
        ],
        [
            'icon'      => '<i class="fa-solid fa-heart"></i>',
            'key'       => 'favourite',
        ],
        [
            'icon'      => '<i class="fa-solid fa-arrow-down"></i>',
            'key'       => 'newest',
        ],
        [
            'icon'      => '<i class="fa-solid fa-arrow-up"></i>',
            'key'       => 'oldest',
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
    'url_free_wallpaper_category'   => [
        'hinh-nen-dien-thoai-mien-phi',
        'free-phone-wallpapers',
        'fonds-d-ecran-pour-telephones-gratuit',
        'fondos-de-pantalla-para-telefonos-gratuito',
        '手机壁纸免费',
        'обои-для-телефона-бесплатно',
        '無料携帯電話の壁紙',
        'wallpaper-ponsel-gratis',
        '무료-휴대폰-배경화면',
        'फोन-वॉलपेपर-मुफ्त',
        'ফোন-ওয়ালপেপার-বিনা-মূল্যে',
        'फोन-वॉलपेपर-मोफत',
        'தொலைபேசி-வால்பேப்பர்கள்-இலவசம்',
        'ఫోన్-వాల్పేపర్లు-నివ్వు',
        'مفت-فون-وال-پیپرز',
        'ફોન-વોલપેપર્સ-મફત',
        'wallpaper-hp-gratis',
        'kertas-dinding-telefon-percuma',
        'วอลเปเปอร์โทรศัพท์ฟรี',
        'ផ្ទាំងរូបភាពទូរស័ព្ទអោយដោយឥតគិតថ្លៃ',
        'ພາບພື້ນຫຼັງໂທລະສັບບໍ່ເສອນ',
        'مجاني-خلفيات-الهاتف',
        'رایگان-تصاویر-زمینه-تلفن',
        'handy-hintergründe-kostenlos',
        'telefon-duvar-kağıtları-ücretsiz',
        'sfondi-per-telefono-gratuito',
        'tapety-na-telefon-darmowy',
        'шпалери-для-телефону-безкоштовно',
        'telefoon-achtergronden-gratis',
        'ταπετσαρίες-για-τηλέφωνα-δωρεάν',
        'telefon-hatterkepek-ingyenes',
        'tapety-na-telefon-zdarma',
        'tapete-pentru-telefon-gratuit',
        'tapety-na-telefon-zadarmo',
        'მობილური-ტელეფონის-ფონები-უფასოდ',
        'חינם-טפטים-לטלפונים',
        'telefon-fon-rasmlari-bepul',
        'papeis-de-parede-para-telefones-gratuito',
        'ಫೋನ್-ವಾಲ್ಪೇಪರ್-ಮುಕ್ತವಾದದು',
    ],
    'url_confirm_page'   => [
        'xac-nhan',
        'confirm',
        'commande',
        'confirmacion',
        '订单确认',
        'आदेश-की-पुष्टि',
        'অর্ডার-নিশ্চিতকরণ',
        'आदेश-पुष्टी',
        'ஆணை-உறுதிப்படுத்தல்',
        'ఆర్డర్-నిర్ధారణ',
        'آرڈر-کی-تصدیق',
        'આદેશ-પુષ્ટિ',
        'ಆರ್ಡರ್-ದೃಢೀಕರಣ',
        '注文確認',
        '주문-확인',
        'konfirmasi-pesanan',
        'pengesahan-pesanan',
        'การยืนยันคำสั่งซื้อ',
        'ការបញ្ជាក់អាជីវកម្ម',
        'підтвердження-замовлення',
        'bestelbevestiging',
        'επιβεβαίωση-παραγγελίας',
        'rendeles-megerősitese',
        'potvrzeni-objednavky',
        'confirmarea-comenzii',
        'potvrdenie-objednavky',
        'წარდგინების-დადასტურება',
        'אישור-הזמנה',
        'buyurtma-tasdiqlash',
        'confirmaçao-de-pedido',
        'ການຢືນຢັນຄຳສັ່ງ',
        'تأكيد-الطلب',
        'تأیید-سفارش',
        'подтверждение-заказа',
        'bestellbestätigung',
        'sipariş-onayı',
        'conferma-dell-ordine',
        'potwierdzenie-zamowienia',
        'konfirmasi-pesanan-id',
    ],
    'url_cart_page'   => [
        'gio-hang',
        'shopping-cart',
        'panier',
        'carrito-de-compras',
        '购物车',
        'शॉपिंग-कार्ट',
        'শপিং-কার্ট',
        'खरेदी-गाडी',
        'ஷாப்பிங்-கார்ட்',
        'షాపింగ్-కార్ట్',
        'خریداری-کی-ٹوکری',
        'ખરીદીટર',
        'ಆಸ್ತೆಗೋಡಿ',
        'ショッピングカート',
        '장바구니',
        'keranjang-belanja',
        'troli-pembelian',
        'ตะกร้าสินค้า',
        'ការចិញ្ចឹមទិន្នន័យ',
        'кошик',
        'winkelwagentje',
        'καλάθι',
        'bevasarlo-kosar',
        'nakupni-košik',
        'coș-de-cumparaturi',
        'nakupny-košik',
        'შოპინგის-კალათ',
        'עגלת-קניות',
        'savat',
        'carrinho-de-compras',
        'ກິບຕາ',
        'عربة-التسوق',
        'سبد-خرید',
        'корзина',
        'warenkorb',
        'alışveriş-sepeti',
        'carrello',
        'koszyk',
        'keranjang-belanja-id',
    ],
    'tool_translate'    => [
        'ai', 'google_translate'
    ],
    'ai_version'    => [ /* vesion đầu tiên được mặc định dùng ở các trường hợp không quy định */
        'gpt-4o-mini', 'gpt-4o',
    ],
];