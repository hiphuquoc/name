import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import purgecss from '@fullhuman/postcss-purgecss';

export default defineConfig({
  plugins: [
    laravel({
        input: [
            'resources/sources/admin/style.scss',
            /* css riêng từng trang */
            'resources/sources/main/home-first-view.scss',
            'resources/sources/main/home-non-first-view.scss',
            'resources/sources/main/category-money-first-view.scss',
            'resources/sources/main/category-money-non-first-view.scss',
            'resources/sources/main/product-first-view.scss',
            'resources/sources/main/product-non-first-view.scss',
            'resources/sources/main/cart-first-view.scss',
            'resources/sources/main/cart-non-first-view.scss',
            'resources/sources/main/page-first-view.scss',
            'resources/sources/main/page-non-first-view.scss',
            'resources/sources/main/category-free-first-view.scss',
            'resources/sources/main/category-free-non-first-view.scss',
            'resources/sources/main/freewallpaper-first-view.scss',
            'resources/sources/main/freewallpaper-non-first-view.scss',
            'resources/sources/main/confirm-first-view.scss',
            'resources/sources/main/confirm-non-first-view.scss',
        ],
        refresh: true,
    }),
  ],
  // // Cấu hình này sẽ loại bỏ những CSS không sử dụng dựa trên nội dung trong các tệp Blade và Vue.
  // css: {
  //   postcss: {
  //     plugins: [
  //       purgecss({
  //         content: [
  //           './resources/views/**/*.blade.php',
  //           './resources/js/**/*.vue'
  //         ],
  //         defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || []
  //       })
  //     ]
  //   }
  // }
});