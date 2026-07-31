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
                'soft': 'var(--shadow-sm)',
                'elevated': 'var(--shadow-md)',
                'floating': 'var(--shadow-lg)',
                'accent': 'var(--accent-glow)',
            },
            borderRadius: {
                'xl': '0.875rem',
                '2xl': '1.125rem',
                '3xl': '1.5rem',
            },
            borderColor: {
                DEFAULT: 'var(--color-brand-dark)',
            },
        },
    },

    plugins: [forms],
};