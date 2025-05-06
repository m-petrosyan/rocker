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
                gray: '#808080',
                graydarker: '#666666',
                graydark: '#101010',
                graydark2: '#262626',
                grayblue: '#607D8B',
                orange: '#FF5722',
                orangeTransparet: '#ff57224d',
                red: '#f44336',
                pwaNavbg: 'rgba(50,62,74,0.58)',
                green: '#4caf50',
                blackTransparent: 'rgba(0,0,0,0.86)',
                blackTransparent2: 'rgba(0,0,0,0.45)'
            },
            fontSize: {
                'sm': '13px'
            }
        }
    },

    plugins: [forms]
};
