const mix = require('laravel-mix');
const path = require('path');
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

mix.webpackConfig({
	resolve: {
        alias: {
            '@views': path.resolve('resources/views'),
            '@components': path.resolve('resources/views/components'),
            '@router': path.resolve('resources/js/router'),
            '@images': path.resolve('resources/assets/images'),
            '@errors': path.resolve('resources/views/errors'),
            '@form': path.resolve('resources/js/form'),
            '@shop': path.resolve('resources/views/shop'),
            '@user': path.resolve('resources/views/auth/user'),
            '@admin': path.resolve('resources/views/auth/admin'),
            '@layout': path.resolve('resources/views/layouts'),
            'vue$': 'vue/dist/vue.runtime.common.js',
        },
    }
});

mix
    .js('resources/js/shop-client.js', 'public/js')
    .js('resources/js/shop-server.js', 'public/js')
    .js('resources/js/admin.js', 'public/js')
    .vue()
    .sass('resources/sass/app.scss', 'public/css');


if (mix.inProduction()) {
	mix.version()
}