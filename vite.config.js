import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        cors: true,
        hmr: {
            host: 'localhost'
        }
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            registerType: 'autoUpdate',
            outDir: 'public/build', // In Laravel, PWA assets should go where Vite builds
            buildBase: '/build/', // Fix unpkg resolution
            injectRegister: 'auto',
            manifest: false, // We're not doing a full web PWA, just Service Worker for Capacitor cache
            workbox: {
                navigateFallback: '/',
                globDirectory: 'public/build',
                globPatterns: ['**/*.{js,css,woff2,png,jpg,jpeg,svg,webp}'],
                // Serve cached files even to Inertia json requests if network fails
                runtimeCaching: [
                    {
                        urlPattern: ({ request }) => request.mode === 'navigate' || request.headers.get('accept').includes('text/html'),
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'clutch-html-cache',
                        },
                    },
                    {
                        // Match Inertia page requests
                        urlPattern: ({ request, url }) => request.headers.get('x-inertia') || url.pathname.startsWith('/'),
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'clutch-inertia-cache',
                            backgroundSync: {
                                name: 'sync-queue',
                                options: { maxRetentionTime: 24 * 60 }
                            }
                        }
                    }
                ]
            }
        }),
    ],
});
