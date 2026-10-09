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
                /**
                 * Tokens oficiais — Manual da Marca CBN
                 * 3.1 Marca | 3.2 Institucionais | 3.3 Informativos
                 */
                cbn: {
                    // 3.1 Marca
                    red: '#E00209',
                    black: '#1E120D',

                    // 3.3 Informativos (base do portal público)
                    navy: '#001D4D',
                    'navy-dark': '#001233',
                    'navy-light': '#026A8E',
                    teal: '#038794',
                    'teal-light': '#69A195',

                    // 3.2 Institucionais (acentos quentes; classes *-gold* mantidas por compatibilidade)
                    gold: '#EFA162',
                    'gold-light': '#F1D6A9',
                    'gold-dark': '#F36529',
                    orange: '#F43517',
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
