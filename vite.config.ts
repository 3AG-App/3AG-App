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
                manualChunks: {
                    'vendor-inertia': ['@inertiajs/react'],
                    'vendor-radix-ui': [
                        '@radix-ui/react-accordion',
                        '@radix-ui/react-alert-dialog',
                        '@radix-ui/react-avatar',
                        '@radix-ui/react-checkbox',
                        '@radix-ui/react-collapsible',
                        '@radix-ui/react-dialog',
                        '@radix-ui/react-dropdown-menu',
                        '@radix-ui/react-label',
                        '@radix-ui/react-popover',
                        '@radix-ui/react-progress',
                        '@radix-ui/react-scroll-area',
                        '@radix-ui/react-select',
                        '@radix-ui/react-separator',
                        '@radix-ui/react-slot',
                        '@radix-ui/react-switch',
                        '@radix-ui/react-tabs',
                        '@radix-ui/react-toggle',
                        '@radix-ui/react-tooltip',
                    ],
                    'vendor-forms': ['react-hook-form', '@hookform/resolvers', 'zod'],
                    'vendor-charts': ['recharts'],
                    'vendor-utils': ['date-fns', 'clsx', 'tailwind-merge', 'class-variance-authority'],
                },
            },
        },
    },
});
