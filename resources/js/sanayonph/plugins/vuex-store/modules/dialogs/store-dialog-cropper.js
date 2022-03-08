export default {
    namespaced: true,

    state: {
        opened : false,
        loading: false,
    },

    getters: {
        state: (state) => {
            return state
        }
    },

    mutations: {
        show(state, payload) {
            state.opened  = true;
            state.loading = false;
        },

        hide(state) {
            state.opened = false;
        },

        load(state, payload) {
            if(payload == null)
                payload = true;
            state.loading = payload;
        },
    }
}
