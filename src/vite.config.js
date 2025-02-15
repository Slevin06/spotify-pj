import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    root: '/var/www/html',
    base: '/',
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost'
        }
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/ts/app.tsx'
            ],
            refresh: true,
        }),
        react(),
    ],
    optimizeDeps: {
        include: ['react', 'react-dom', 'react-router-dom', 'lucide-react']
    },
    resolve: {
        alias: {
            '@': '/var/www/html/resources/ts'
        }
    }
});