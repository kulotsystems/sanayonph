/*
|--------------------------------------------------------------------------
| VUEX STORE MODULE: icon
|--------------------------------------------------------------------------
| Configurable icons for entities
|
*/

export default {

    namespaced: true,

    state: {
        sign_in : 'arrow_forward',
        sign_out: 'logout',
        address : 'place',
        category: 'category',
        product : 'shopping_bag',
        delivery: 'send',
        payment : 'payment',
        buy     : 'shopping_basket',
        order   : 'credit_score',
        action  : 'commit'
    },

    getters: {
        state: (state) => {
            return state
        }
    },

}
