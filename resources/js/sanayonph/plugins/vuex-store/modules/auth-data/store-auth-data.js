export default {
    namespaced: true,

    state: {
        addresses : null,
        categories: null,
        products  : null,
        orders    : null
    },

    getters: {
        addresses: (state) => {
            return state.addresses;
        },
        categories: (state) => {
            return state.categories;
        },
        products: (state) => {
            return state.products;
        },
        orders: (state) => {
            return state.orders;
        },
    },

    mutations: {
        fill(state, payload) {
            state[payload.key] = payload.data;
        },
        purge(state, key) {
            if(key != null) {
                if(state[key] != null)
                    state[key] = null;
            }
        }
    }
}
