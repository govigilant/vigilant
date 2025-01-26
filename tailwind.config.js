import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

const colors = {
    base: {
        black: '#100F0F',
        950: '#1C1B1A',
        900: '#282726',
        850: '#343331',
        800: '#403E3C',
        700: '#575653',
        600: '#6F6E69',
        500: '#878580',
        300: '#B7B5AC',
        200: '#CECDC3',
        150: '#DAD8CE',
        100: '#E6E4D9',
        50: '#F2F0E5',
        paper: '#FFFCF0',
    },
    red: {
        DEFAULT: '#AF3029',
        light: '#D14D41',
    },
    orange: {
        DEFAULT: '#BC5215',
        light: '#DA702C',
    },
    yellow: {
        DEFAULT: '#AD8301',
        light: '#D0A215',
    },
    green: {
        DEFAULT: '#0D5413',
        light: '#337F1F',
    },
    cyan: {
        DEFAULT: '#24837B',
        light: '#3AA99F',
    },
    blue: {
        DEFAULT: '#205EA6',
        light: '#4385BE',
    },
    purple: {
        DEFAULT: '#5E409D',
        light: '#8B7EC8',
    },
    magenta: {
        DEFAULT: '#A02F6F',
        light: '#CE5D97',
    },
};

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './packages/*/resources/views/**/*.blade.php',
        './vendor/ramonrietdijk/livewire-tables/resources/**/*.blade.php',
        './vendor/vigilant/*/resources/views/**/*.blade.php'
    ],

    theme: {
        extend: {
            colors: colors,

            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};
