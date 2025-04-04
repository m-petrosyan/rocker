import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans]
            },
            colors: {
                gray: 'var(--gray)',
                graydarker: '#666666',
                graydark: '#141414',
                orange: '#FF5722',
                red: '#f44336',
                pwaNavbg: 'rgba(50,62,74,0.58)'
            },
            fontSize: {
                'sm': '13px'
            }
        }
    },

    plugins: [forms]
};
