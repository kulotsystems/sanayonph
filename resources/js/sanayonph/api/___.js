import axios  from 'axios';
import store  from '../plugins/vuex-store/Store.js'
import router from '../plugins/vue-router/Router.js';
import pace   from 'pace-js';

// create and configure an axios instance
let api = axios.create({
    withCredentials: false,
});
api.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// intercept request
api.interceptors.request.use(
     (config) =>  {
         // intercept :username Router param
         if(router.history.current.params.username)
             config.headers['Username'] = router.history.current.params.username;

         // intercept :delivery_address Router param
         if(router.history.current.params.delivery_address)
             config.headers['DeliveryAddress'] = router.history.current.params.delivery_address;

         // intercept :store Router param
         if(router.history.current.params.store)
             config.headers['Store'] = router.history.current.params.store;

         // intercept :category Router param
         if(router.history.current.params.category)
             config.headers['Category'] = router.history.current.params.category;

         // intercept :product Router param
         if(router.history.current.params.product)
             config.headers['Product'] = router.history.current.params.product;

         // intercept :order Router param
         if(router.history.current.params.order)
             config.headers['Order'] = router.history.current.params.order;

         pace.restart();
         return config;
    },
    (errors) => {
         pace.stop();
         return Promise.reject(errors);
    }
);

// intercept response
api.interceptors.response.use(
    (response) => {
        pace.stop();

        return response;
    },
    (errors) => {
        pace.stop();

        // 400 Bad Request
        if(errors.response.status === 400)
            store.commit('dialog/message/error400', router.history.base);

        // 401 Unauthorized
        else if(errors.response.status === 401) {
            store.commit('auth/setUser', null);
            if(router.history.current.name === 'sign-in')
                store.commit('auth/toggleTry');
            else
                store.commit('auth/forceOut');
        }

        // 403 Forbidden
        else if(errors.response.status === 403) {
            if(errors.headers.correct_username != null && errors.headers.wrong_username != null) {
                const correct_url = router.history.base + router.history.current.fullPath.replace(errors.headers.wrong_username, errors.headers.correct_username);
                store.commit('dialog/message/error403', correct_url);
            }
            else
                store.commit('dialog/message/error403', router.history.base);
        }

        // 404 Not Found
        else if(errors.response.status === 404)
            store.commit('dialog/message/error404', router.history.base);

        // 419 CSRF Token Mismatch
        else if(errors.response.status === 419)
            store.commit('dialog/message/csrf');

        // other errors
        else
            return Promise.reject(errors);
    }
);

export default api;
