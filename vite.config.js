import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/marketing.css',
        'resources/js/marketing.js'
      ],
      fonts: [
        bunny('Inter', {
          alias: 'sans',
          weights: [400, 500, 600],
        }),
        bunny('JetBrains Mono', {
          alias: 'mono',
          weights: [400, 500],
        }),
      ],
      refresh: true,
    }),
    tailwindcss(),
  ],
  server: {
    watch: {
      ignored: ['**/storage/framework/views/**'],
    },
  },
});
