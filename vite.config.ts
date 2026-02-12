import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ],
    esbuild: {
        jsx: 'automatic',
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (!id.includes('/node_modules/')) {
                        return;
                    }

                    if (id.includes('/node_modules/@floating-ui/')) {
                        return 'vendor-floating-ui';
                    }

                    if (id.includes('/node_modules/@inertiajs/')) {
                        return 'vendor-inertia';
                    }

                    if (id.includes('/node_modules/@radix-ui/')) {
                        return 'vendor-radix-ui';
                    }

                    if (
                        id.includes('/node_modules/react-hook-form/') ||
                        id.includes('/node_modules/@hookform/resolvers/') ||
                        id.includes('/node_modules/zod/')
                    ) {
                        return 'vendor-forms';
                    }

                    if (id.includes('/node_modules/recharts/')) {
                        return 'vendor-charts';
                    }

                    if (
                        id.includes('/node_modules/date-fns/') ||
                        id.includes('/node_modules/clsx/') ||
                        id.includes('/node_modules/tailwind-merge/') ||
                        id.includes('/node_modules/class-variance-authority/')
                    ) {
                        return 'vendor-utils';
                    }
                },
            },
        },
    },
});
