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
                    base: 'rgb(var(--brand-base) / <alpha-value>)',
                    muted: 'rgb(var(--brand-muted) / <alpha-value>)',
                    neon: 'rgb(var(--brand-neon) / <alpha-value>)',
                    dark: 'rgb(var(--brand-dark) / <alpha-value>)',
                    black: 'rgb(var(--brand-black) / <alpha-value>)',
                    surface: 'rgb(var(--brand-surface) / <alpha-value>)',
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
                DEFAULT: 'rgb(var(--brand-dark) / 1)',
            },
        },
    },

    plugins: [forms],
};