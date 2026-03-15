import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['bootstrap'],
                    utils: ['axios'],
                    // Séparer le CSS pour un meilleur cache
                    styles: ['resources/sass/app.scss']
                }
            }
        },
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true
            }
        },
        cssCodeSplit: true,
        sourcemap: false,
        // Optimisation du chunk size
        chunkSizeWarningLimit: 1000,
        // Activer le code splitting automatique
        target: 'esnext'
    },
    server: {
        hmr: {
            overlay: false
        }
    },
    // Optimisation pour la production
    define: {
        'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'production')
    }
});
