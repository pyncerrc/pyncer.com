module.exports = {
    content: ["./private/code/Component/**/*.php"],
    darkMode: 'class',
    theme: {
        borderWidth: {
            DEFAULT: '0.0625rem',
            '0': '0',
            '2': '0.09375rem',
            '4': '0.25rem',
            '8': '0.5rem',
        },
        screens: {
            'xs': '480px',
            'sm': '640px',
            'md': '768px',
            'lg': '1024px',
            'xl': '1280px',
            '2xl': '1536px',
        },
        fontSize: {
            xs: ['0.75rem', '1rem'],
            sm: ['0.875rem', '1.25rem'],
            base: ['1rem', '1.5rem'],
            lg: ['1.125rem', '1.75rem'],
            xl: ['1.25rem', '1.75rem'],
            '2xl': ['1.5rem', '2rem'],
            '3xl': ['2rem', '2.25rem'],
            '4xl': ['2.25rem', '2.5rem'],
        },
        extend: {},
    },
    plugins: [],
}
