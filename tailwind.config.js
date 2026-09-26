import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // Font utama aplikasi
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            // Warna merah khas aplikasi, dipakai dengan class seperti bg-brand, text-brand-dark
            colors: {
                brand: {
                    light: '#FBE4E4',
                    DEFAULT: '#C41E1E',
                    dark: '#9E1616',
                },
            },
        },
    },

    plugins: [forms],
};
