import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    DEFAULT: '#0f6e8c',
                    light: '#c1eaff',
                    dark: '#00556d',
                },
                primary: {
                    DEFAULT: '#0f6e8c',
                },
                error: {
                    DEFAULT: '#ba1a1a',
                },
                tertiary: {
                    DEFAULT: '#724200',
                },
                'secondary-container': {
                    DEFAULT: '#d5e0f8',
                },
                'on-secondary-container': {
                    DEFAULT: '#586377',
                },
                'error-container': {
                    DEFAULT: '#ffdad6',
                },
                'on-error-container': {
                    DEFAULT: '#93000a',
                },
                'on-error': {
                    DEFAULT: '#ffffff',
                },
                surface: {
                    DEFAULT: '#f7f9fb',
                    dim: '#d8dadc',
                    bright: '#f7f9fb',
                    container: {
                        lowest: '#ffffff',
                        low: '#f2f4f6',
                        DEFAULT: '#eceef0',
                        high: '#e6e8ea',
                        highest: '#e0e3e5',
                    },
                },
                'on-surface': {
                    DEFAULT: '#191c1e',
                    variant: '#3f484d',
                },
                outline: {
                    DEFAULT: '#6f787e',
                    variant: '#bfc8cd',
                },
            },
            spacing: {
                'section-gap': '80px',
                'margin-desktop': '48px',
                'margin-mobile': '16px',
            },
            animation: {
                float: 'float 3s ease-in-out infinite',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-8px)' },
                },
            },
        },
    },

    plugins: [forms],
};
