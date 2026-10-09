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
                 * Portal tratado como peça institucional (3.2) sobre base da marca (3.1).
                 * Classes *-navy* / *-gold* mantidas por compatibilidade com os componentes.
                 */
                cbn: {
                    // 3.1 Marca
                    red: '#E00209',
                    black: '#1E120D',

                    // Base escura do portal = preto da marca (ex-navy)
                    navy: '#1E120D',
                    'navy-dark': '#140E0A',
                    'navy-light': '#3A2E28',

                    // 3.2 Institucionais (acentos / CTAs — ex-gold)
                    gold: '#EFA162',
                    'gold-light': '#F1D6A9',
                    'gold-dark': '#F36529',
                    orange: '#F43517',

                    // 3.3 Informativos (disponíveis, sem protagonismo no portal)
                    teal: '#038794',
                    'teal-light': '#69A195',
                    info: '#026A8E',
                    'info-dark': '#001D4D',
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
