import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/livewire/flux-pro/stubs/**/*.blade.php',
        './vendor/livewire/flux/stubs/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'teal': '#007393',
                'light-teal': '#00A8D6',
                'dark-teal': '#00607A',
                'forest-green': '#005127',
                'maroon': '#B30000',
                'deep-purple': '#281E30',
                'pink': '#933258',
                'beige': '#D1B591',
                'orange': '#FF6857',
                'dark-orange': '#A81100',
            }
        },
    },

    plugins: [forms],
};
