export default {
    namespaced: true,

    state: {
        opened: false,
        prompt: '',
    },

    getters: {
        state: (state) => {
            return state
        }
    },

    mutations: {
        show(state, payload) {
            state.opened = true;
            state.prompt = '';
            if(payload) {
                if(payload.prompt)
                    state.prompt = payload.prompt;
            }
        },

        hide(state) {
            state.opened = false;
        }
    }
}
