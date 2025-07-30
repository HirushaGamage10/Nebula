import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/styles.min.css",
                "resources/libs/jquery/dist/jquery.min.js",
                "resources/libs/bootstrap/dist/js/bootstrap.bundle.min.js",
                "resources/js/sidebarmenu.js",
                "resources/js/app.min.js",
                "resources/js/jquery-3.6.0.min.js",
                "resources/libs/simplebar/dist/simplebar.js",
                "resources/js/dashboard.js",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@backgroundImage": path.resolve(__dirname, "resources/image/background"),
            "@generalImage": path.resolve(__dirname, "resources/image/general"),
        },
    },
    server: {
        host: 'localhost',
        port: 9283,
        // hmr: {
        //     host: 'angry-onions-smash.loca.lt',
        //     protocol: 'wss',  // WebSockets over SSL/TLS
        //     clientPort: 443,  // Default HTTPS port
        // },
    },
    build: {
        outDir: 'public/build',  // Output directory
        manifest: true,  // Generate manifest for Laravel
    },
    // base: 'https://angry-onions-smash.loca.lt/',
});
