import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/css/main.scss'],
            ssr: 'resources/js/ssr.js',
            refresh: true
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false
                }
            }
        }),
        VitePWA({
            registerType: 'autoUpdate',
            includeAssets: [
                'icon-192.png',
                'icon-512.png',
                'fonts/Helvetica.ttf',
                'fonts/Helvetica-Bold.ttf'
            ],
            manifest: {
                name: 'Rocker.am',
                short_name: 'Rocker events',
                start_url: '/',
                display: 'standalone',
                background_color: '#000000',
                theme_color: '#000000',
                icons: [
                    {
                        src: 'icons/icon-192.png',
                        sizes: '192x192',
                        type: 'image/png'
                    },
                    {
                        src: 'icons/icon-512.png',
                        sizes: '512x512',
                        type: 'image/png'
                    }
                ]
            },
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,ttf}'],
                runtimeCaching: [
                    {
                        urlPattern: ({ url }) => url.pathname.includes('ziggy.js'),
                        handler: 'NetworkOnly' // Bypass ziggy.js
                    }
                ]
            }
        })
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            'ziggy-js': '/resources/js/ziggy.js'
        }
    },
    ssr: {
        noExternal: ['@splidejs/vue-splide']
    }
});
