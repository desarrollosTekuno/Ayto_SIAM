//tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            screens: {
                hd: '1920px',
                lp: '1366px',
            },
            colors: {
                primary: '#7D2447',
                secondary: '#4A5350',
                tertiary: '#BFB591',
                complement1: '#838386',
                complement2: '#404A6E',
                complement3: '#B5ACAF',
            },
            fontSize: {
                mxs: '0.666rem',
                xxs: '0.565rem',
                53: '15rem',
            },
        },
    },

    plugins: [forms, typography],
};
