const mix = require('laravel-mix');
const path = require('path');

console.log(path.join(__dirname, 'build', 'contracts'));
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

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .sourceMaps()
   .webpackConfig({
       resolve: {
            alias: {
                root: path.join(__dirname, 'resources', 'assets', 'js'),
                contracts: path.join(__dirname, 'build', 'contracts'),
                components: path.join(__dirname, 'resources', 'assets', 'js', 'components')
            }
       },

       module: {
           rules: [
                {
                    test: /\.(js|vue)$/,
                    loader: 'eslint-loader',
                    enforce: 'pre',
                    exclude: [/node_modules/],
                    options: {
                        fix: true
                    }
                }
           ]
       }
   });
