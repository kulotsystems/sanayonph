import api from './___.js';
const  url = '/api/store';

export default {

    user() {
        return api.get(url);
    },

    products() {
        return api.get(`${url}/products`);
    },

    product(id, action) { // action = 'display' | 'buy'
        if(action == null)
            action = 'display';
        return api.get(`${url}/products/${id}/${action}`);
    },

    merchantCode() {
        return api.get(`${url}/merchant-code`);
    }

}
