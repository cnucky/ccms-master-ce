let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/user/app.js', 'public/static/js')
    .js('resources/assets/js/admin/app.js', 'public/static/admin/js')
    .sass('resources/assets/sass/app.scss', 'public/static/css')
    .copy('semantic/dist/', 'public/static/semantic-ui')
;