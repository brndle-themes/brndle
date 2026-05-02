import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin'
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin';

if (! process.env.APP_URL) {
  process.env.APP_URL = 'http://brndle.test';
}

export default defineConfig({
  base: '/wp-content/themes/brndle/public/build/',
  plugins: [
    tailwindcss(),
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/dark-mode.js',
        'resources/js/view-transitions.js',
        'resources/css/editor.css',
        'resources/js/editor.js',
      ],
      refresh: true,
      // Sage v11.2 pattern — let Laravel-Vite hash + emit images and fonts
      // through the build directory so PHP can resolve them via `Vite::asset()`.
      // Replaces the prior `import.meta.glob()` trick in resources/js/app.js,
      // which produced an empty 0 B JS artifact in every build.
      assets: ['resources/images/**', 'resources/fonts/**'],
    }),

    wordpressPlugin(),

    wordpressThemeJson({
      disableTailwindColors: false,
      disableTailwindFonts: false,
      disableTailwindFontSizes: false,
      disableTailwindBorderRadius: false,
    }),
  ],
  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles': '/resources/css',
      '@fonts': '/resources/fonts',
      '@images': '/resources/images',
    },
  },
})
