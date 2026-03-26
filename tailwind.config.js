import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    base: 'var(--color-brand-base)',
                    muted: 'var(--color-brand-muted)',
                    neon: 'var(--color-brand-neon)',
                    dark: 'var(--color-brand-dark)',
                    black: 'var(--color-brand-black)',
                    surface: 'var(--color-brand-surface)',
                }
            },
            boxShadow: {
                'neon': '0 0 10px rgba(12, 225, 181, 0.5)',
                'neon-hover': '0 0 20px rgba(12, 225, 181, 0.7)',
            },
            borderColor: {
                DEFAULT: 'var(--color-brand-dark)',
            },
        },
    },

    plugins: [forms],
};