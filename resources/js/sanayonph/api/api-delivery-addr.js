import api from './___.js';
const  url = '/api/delivery-addr';

export default {

    index() {
        return api.get(url);
    },

    store(request) {
        return api.post(url, request);
    },

    edit(id) {
        return api.get(`${url}/edit/${id}`);
    },

    update(request, id) {
        return api.put(`${url}/${id}`, request);
    },

    delete(id) {
        return api.delete(`${url}/${id}`);
    }

}
