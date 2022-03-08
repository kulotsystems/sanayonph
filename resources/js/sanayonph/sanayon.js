/*
|--------------------------------------------------------------------------
| APP ENTRY POINT
|--------------------------------------------------------------------------
| The entry script for the Single Page Application (SPA)
|
*/


require('../bootstrap');
import Vue from 'vue';

// --- IMPORTS -----------------------------------------------------------
const Sanayon = () => import('./Sanayon.vue');
import router  from './plugins/vue-router/Router.js';
import store   from './plugins/vuex-store/Store.js';
import vuetify from './plugins/vuetify/Vuetify.js';


// --- APP INSTANCE ------------------------------------------------------
const app = new Vue({
    el     : '#main',
    router : router,
    store  : store,
    vuetify: vuetify,
    render : h => h(Sanayon)
});
