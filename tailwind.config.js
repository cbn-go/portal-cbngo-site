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
                 * Manual da Marca CBN — portal como peça institucional (3.2).
                 * Classes *-navy* / *-gold* mantidas por compatibilidade.
                 */
                cbn: {
                    // 3.1 Marca
                    red: '#E00209',
                    black: '#1E120D',

                    // 3.2 Institucionais — protagonistas do portal
                    orange: '#F43517',
                    'orange-mid': '#F36529',
                    gold: '#EFA162',
                    'gold-light': '#F1D6A9',
                    'gold-dark': '#F36529',

                    // Alias usados nos componentes (antes "navy") → laranjas 3.2
                    navy: '#F43517',
                    'navy-dark': '#D92E12',
                    'navy-light': '#F36529',

                    // 3.3 Informativos (secundário)
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
