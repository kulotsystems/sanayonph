import api from './___.js';
const  url = '/api/product';

export default {

    index() {
        return api.get(url);
    },

    list() {
        return api.get(`${url}/list`);
    },

    store(request) {
        return api.post(url, request);
    },

    show(id) {
        return api.get(`${url}/${id}`);
    },

    edit(id) {
        return api.get(`${url}/edit/${id}`);
    },

    update(request, id) {
        return api.put(`${url}/${id}`, request);
    },

    delete(id) {
        return api.delete(`${url}/${id}`);
    },

    uploadPhoto(request) {
        return api.post(`${url}/photo`, request);
    },

    publish(request, id) {
        return api.put(`${url}/publish/${id}`, request);
    },

    dependencies() {
        return api.get(`${url}/dependencies`);
    },

}
