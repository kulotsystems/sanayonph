/*
|--------------------------------------------------------------------------
| VUEX STORE MODULE: tab
|--------------------------------------------------------------------------
| Keep track of align-with-title tabs on top of the app
|
*/


export default {
    namespaced: true,

    state: {
        'profile-edit': 0,
        'profile-store-products-add' : 0,
        'profile-store-products-edit': 0,
        'profile-orders': 0,
        'profile-store-orders': 0,

        history: {},
        index  : {}
    },

    getters: {
        history: (state) => {
            return state.history;
        },
        index: (state) => {
            return state.index;
        }
    },

    mutations: {
        clearHistory(state, routeName) {
            state.history[routeName] = [];
            state.index[routeName]   = -1;
        },
        pushHistory(state, payload) {
            if(!state.history[payload.routeName])
                state.history[payload.routeName] = [];

            let totalHistory = state.history[payload.routeName].length;

            let proceedPush = false;
            if(state.index[payload.routeName] < (totalHistory - 1)) {
                if(state.history[payload.routeName][state.index[payload.routeName]]['tabID'] !== payload.tabID) {
                    proceedPush = true;
                    state.history[payload.routeName].splice(state.index[payload.routeName] + 1);
                }
            }
            else {
                if(totalHistory <= 0)
                    proceedPush = true;
                else if(state.history[payload.routeName][totalHistory-1]['tabID'] !== payload.tabID)
                    proceedPush = true;
            }

            if(proceedPush) {
                state.history[payload.routeName].push({
                    tabID : payload.tabID,
                    tabKey: payload.tabKey
                });
                state.index[payload.routeName] = state.history[payload.routeName].length - 1;
            }
        },
        popHistory(state, payload) {
            if(!state.history[payload.routeName])
                state.history[payload.routeName] = [];

            let index = state.index[payload.routeName];

            // initialize left and right tab ids
            let leftTabID  = '';
            let rightTabID = '';

            // compute left and right index
            let leftIndex  = index - 1;
            let rightIndex = index + 1;

            // if browser back button is clicked
            let proceed = true;
            if(leftIndex >= 0) {
                if(state.history[payload.routeName][leftIndex]['tabID'] === payload.tabID && state.history[payload.routeName][leftIndex]['tabKey'] === payload.tabKey) {
                    state.index[payload.routeName] = leftIndex;
                    proceed = false;
                }
            }
            else {
                state.index[payload.routeName] = -1;
                proceed = false;
            }

            // else if browser forward button is clicked
            if(proceed) {
                if (rightIndex < state.history[payload.routeName].length) {
                    if (state.history[payload.routeName][rightIndex]['tabID'] === payload.tabID && state.history[payload.routeName][rightIndex]['tabKey'] === payload.tabKey)
                        state.index[payload.routeName] = rightIndex;
                }
            }
        },
    }
}
