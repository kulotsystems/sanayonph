/*
|--------------------------------------------------------------------------
| VUE ROUTER
|--------------------------------------------------------------------------
| An instance of the vue-router that defines all the SPA front-end routes.
|
*/


import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);


// --- UTILITIES ----------------------------------------------------------
import auth    from './routes/routes-auth.js';
import profile from './routes/routes-profile.js';
import shop    from './routes/routes-shop.js';
import explore from './routes/routes-explore.js';


// ...                                                                  ...
import init   from './routes/routes-init.js';
import fin    from './routes/routes-fin.js'
// ...                                                                  ...


export default new VueRouter({
    mode  : 'history',
    base  : '/app',
    routes: [].concat(
        init,
        auth,
        shop,
        explore,
        profile,
        fin
    ),
});
