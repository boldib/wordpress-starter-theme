/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './src/**/*.{js,jsx,ts,tsx}',
    './*.php',
    './inc/**/*.php',
    './parts/**/*.{html,php}',
    './templates/**/*.{html,php}'
  ],
  theme: {
    extend: {
      screens: {
        'md': '782px',
      },
    },
  },
  plugins: [],
}
