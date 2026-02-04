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

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                // Si vols una font més "tech", pots importar 'Orbitron' o 'Roboto' aquí
            },
            colors: {
                // AQUI POSEM LA TEVA PALETA:
                brand: {
                    base:   '#27B699', // Verd Base
                    muted:  '#358B7A', // Verd Muted
                    neon:   '#0CE1B5', // EL NEON BRILLANT ✨
                    dark:   '#356158', // Verd Fosc (detalls)
                    black:  '#273633', // FONS PRINCIPAL
                    surface:'#343B39', // FONS DE TARGETES
                }
            },
            boxShadow: {
                // Creem una ombra de neon personalitzada
                'neon': '0 0 10px rgba(12, 225, 181, 0.5)', 
                'neon-hover': '0 0 20px rgba(12, 225, 181, 0.7)', 
            }
        },
    },

    plugins: [forms],
};