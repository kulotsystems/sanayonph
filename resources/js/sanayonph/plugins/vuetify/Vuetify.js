/*
|--------------------------------------------------------------------------
| VUETIFY
|--------------------------------------------------------------------------
| An instance of the User Interface Framework for the SPA
|
*/

import '@fontsource/roboto';
import 'material-design-icons-iconfont/dist/material-design-icons.css';
import 'vuetify/dist/vuetify.min.css';
import 'pace-js/themes/orange/pace-theme-minimal.css';

import Vue from 'vue';
import Vuetify from 'vuetify';
Vue.use(Vuetify);


export default new Vuetify({
    icons: {
        iconfont: 'mdiSvg',
    },
    theme: {
        themes: {
            light: {
                primary  : '#b75c08',
                yellow   : '#fee205',
                secondary: '#8f5e36',
                //info   : '#ffaa2c',
                //error  : '#f83e70'
            }
        }
    }
});


