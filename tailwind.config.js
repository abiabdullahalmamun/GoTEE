import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/View/Components/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: 'var(--primary)',
                'primary-opt': 'var(--primary-opt)',
                'primary-dark': 'var(--primary-dark)',
                'label-primary': 'var(--label-primary)',
                'label-primary-light': 'var(--label-primary-light)',
            },
        },
    },

    plugins: [forms],
};
