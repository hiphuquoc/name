<?php 

return [
    'default'               => '/storage/images/image-default-660x660.png',
    'folder_upload'         => 'public/images/upload/',
    'extension'             => 'webp',
    'quality'               => '90',
    'resize_large_width'    => 800,
    'resize_small_width'    => 500,
    'resize_mini_width'     => 50,
    /* danh sách action: copy_url, change_name, change_image, delete */
    'keyType'               => '-type-',
    'type'                  => [
        'default'               => ['copy_url', 'change_image'],
        'manager-upload'        => ['copy_url', 'change_name', 'change_image', 'delete']
    ],
    'loading_main_css'          => '/storage/images/loading-gif-1-min.svg',
    'loading_main_gif'          => 'public/images/loading-gif-1-min.svg',
    'loading_main_gif_small'    => 'public/images/loading-gif-1-200.svg'
];