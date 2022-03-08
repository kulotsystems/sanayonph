/*
|--------------------------------------------------------------------------
| VUEX STORE MODULE: history
|--------------------------------------------------------------------------
| window.history helpers
|
*/

export default {

    namespaced: true,

    state: {
        navLefts : {},
        navLeft  : null,
        navRight : null,
        goBack   : 0,
    },

    getters: {
        navLefts: (state) => {
            return state.navLefts;
        },
        navLeft: (state) => {
            return state.navLeft;
        },
        navRight: (state) => {
            return state.navRight;
        },
        goBack: (state) => {
            return state.goBack;
        },
    },

    mutations: {
        pushLeft(state, payload) {
            state.navLefts[payload.name] = true;
        },
        popLeft(state, payload) {
            state.navLefts[payload.name] = false;
            delete state.navLefts[payload.name];
        },
        goBack(state, payload) {
            state.goBack += 1;
            state.navLeft  = payload.navLeft;
            state.navRight = payload.navRight;
        },
    }

}
