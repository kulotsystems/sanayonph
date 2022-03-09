const mix = require('laravel-mix');

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

mix
    .disableNotifications()
    .sourceMaps(false)
    //.browserSync('127.0.0.1:8000')

    .js('resources/js/sanayonph/sanayon.js', 'public/js/sanayon.js')

    .js('resources/js/vendor.js', 'public/js/vendor/___.js')
    .extract(['vue', 'vuex', 'vue-router'], 'js/vendor/packages/vue.js')
    .extract(['vuetify']   , 'js/vendor/packages/vuetify.js')
    .extract(['@fontsource'] , 'js/vendor/packages/roboto.js')
    .extract(['material-design-icons-iconfont'], 'js/vendor/packages/material-icons.js')
    .extract(['axios']     , 'js/vendor/packages/axios.js')
    .extract(['cropperjs'] , 'js/vendor/packages/cropperjs.js')
    .extract(['date-fns']  , 'js/vendor/packages/date-fns.js')
    .extract(['pace-js']   , 'js/vendor/packages/pace.js')
    .extract()

    .vue()
    .version();


let dir = 'dev';
let ids = 'named';
if(mix.inProduction()) {
    dir = 'prod';
    ids = 'deterministic';
}

mix.webpackConfig({
    output: {
        chunkFilename: (pathData) => {
            return pathData.chunk.name === null ? 'js/' + dir + '/[name].js?[contenthash]' : '[name].js';
        },
    },
    optimization: {
        chunkIds: ids
    }
});
