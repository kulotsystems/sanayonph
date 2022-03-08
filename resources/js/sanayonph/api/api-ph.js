import api from './___.js';
const  url = '/api/phaddr';

export default {

    regions() {
        return api.get(`${url}/regions`);
    },

    provinces(request) {
        return api.get(`${url}/provinces`, {params: request});
    },

    muncities(request) {
        return api.get(`${url}/muncities`, {params: request});
    },

    barangays(request) {
        return api.get(`${url}/barangays`, {params: request});
    }

}
