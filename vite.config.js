import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import basicSsl from '@vitejs/plugin-basic-ssl';

/* export style */
export default defineConfig({
  plugins: [
    laravel({
        input: [
            'resources/sources/main/style.scss', 
            'resources/sources/admin/style.scss'
        ],
        refresh: true
    }),
    // basicSsl()
  ]
});

// /* xóa file map => tính năng replace theme color */
// const path = './public/build/assets/style-main.css';
// if (fs.existsSync(path)) {
//   fs.unlinkSync(path);
// }

