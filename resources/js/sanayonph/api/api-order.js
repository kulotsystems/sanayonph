import api from './___.js';
const  url = '/api/order';

export default {

    placeOrder(id, request) {
        return api.post(`${url}/place/${id}`, request);
    },

    buyerIndex() {
        return api.get(url);
    },

    buyerShow(id) {
        return api.get(`${url}/${id}`);
    },

    buyerCancel(id, request) {
        return api.put(`${url}/${id}`, request);
    },

    buyerReceive(id) {
        return api.post(`${url}/${id}`);
    },

    screenshot(id, request) {
        return api.post(`${url}/${id}/screenshot`, request);
    },

    sellerIndex() {
        return api.get(`${url}/seller`);
    },

    sellerShow(id) {
        return api.get(`${url}/seller/${id}`);
    },

    sellerDecline(id, request) {
        return api.put(`${url}/seller/${id}`, request);
    },

    sellerConfirm(id, request) {
        return api.post(`${url}/seller/${id}`, request);
    }

}
