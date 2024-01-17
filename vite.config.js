import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
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
    basicSsl()
  ]
});

