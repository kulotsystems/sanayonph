/*
|--------------------------------------------------------------------------
| VUEX STORE MODULE: chat
|--------------------------------------------------------------------------
| Messenger Chat Plugin helpers
|
*/

export default {
    namespaced: true,

    state: {
        root: document.getElementById('fb-root')
    },

    mutations: {
        show(state) {
            state.root.style.display = 'block';
        },
        hide(state) {
            state.root.style.display = 'none';
        }
    }
}
