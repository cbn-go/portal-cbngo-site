import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                cbn: {
                    navy: '#0D2240',
                    'navy-dark': '#071324',
                    'navy-light': '#163660',
                    gold: '#C89D3C',
                    'gold-light': '#DFC16B',
                    'gold-dark': '#9E7B28',
                },
            },
            fontFamily: {
                sans: ['"Humanist 521"', '"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                serif: ['"Baker Signet"', '"Playfair Display"', ...defaultTheme.fontFamily.serif],
                'brand-serif': ['"Baker Signet"', 'serif'],
                'brand-sans': ['"Humanist 521"', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
