/*
|--------------------------------------------------------------------------
| VUEX STORE MODULE: store
|--------------------------------------------------------------------------
| Store (Seller) helpers
|
*/


export default {

    namespaced: true,

    state: {
        user    : null,
        loaded  : false,
        stores  : [],
        catalogs: {},
        products: {}
    },

    getters: {
        user: (state) => {
            return state.user;
        },
        loaded: (state) => {
            return state.loaded
        },
        avatar: (state) => {
            return state.user.avatar != null ? state.user.avatar : '';
        },
        stores: (state) => {
            return state.stores;
        },
        catalogs: (state) => {
            return state.catalogs;
        },
        products: (state) => {
            return state.products;
        }
    },

    mutations: {
        setUser(state, user) {
            state.user = user;
        },
        load(state) {
            state.loaded = true;
        },
        setStores(state, stores) {
            state.stores = stores;
        },
        addCatalog(state, payload) {
            state.catalogs[`${payload.username}_${payload.store}`] = payload.categories;
        },
        addProduct(state, payload) {
            state.products[`${payload.username}_${payload.store}_${payload.product.id}`] = payload.product;
        }
    }
}
