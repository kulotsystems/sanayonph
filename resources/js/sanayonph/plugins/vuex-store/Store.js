/*
|--------------------------------------------------------------------------
| VUEX STORE
|--------------------------------------------------------------------------
| An instance of the vuex-store that serves as the SPA centralized storage
|
*/


import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);


// --- MODULES ------------------------------------------------------------
import auth     from './modules/store-auth.js';
import chat     from './modules/store-chat.js';
import dialog   from './modules/store-dialog.js';
import env      from './modules/store-env.js';
import form     from './modules/store-form.js';
import history  from './modules/store-history.js';
import icon     from './modules/store-icon.js';
import path     from './modules/store-path.js';
import snackbar from './modules/store-snackbar.js';
import store    from './modules/store-store.js';
import tab      from './modules/store-tab.js';


export default new Vuex.Store({

    state: {

    },

    getters: {

    },

    mutations: {

    },

    actions: {

    },

    modules: {
        auth,
        chat,
        dialog,
        env ,
        form,
        history,
        icon,
        path,
        snackbar,
        store,
        tab
    }

});




