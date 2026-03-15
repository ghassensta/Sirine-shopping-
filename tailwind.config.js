/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/views/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: "#6741d9",
          light: "#a99ff3",
          dark: "#3d2484",
        },
        secondary: {
          DEFAULT: "#ffbe76",
          light: "#ffe3ba",
          dark: "#b08a4f",
        },
      },
      fontFamily: {
        sans: ["Poppins", "Inter", "sans-serif"],
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/line-clamp'),
  ],
  // PurgeCSS pour la production
  purge: {
    enabled: process.env.NODE_ENV === 'production',
    content: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './resources/**/*.vue',
      './resources/views/**/*.php',
    ],
    options: {
      safelist: [
        // Conserver les classes utilisées dynamiquement
        'swiper-*',
        'fa-*',
        /^bg-/,
        /^text-/,
        /^border-/,
        /^hover:/,
        /^focus:/,
        /^active:/,
        /^group-hover:/,
        /^dark:/,
      ],
    },
  },
};
