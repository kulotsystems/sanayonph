import api from './___.js';
const  url = '/api/user';

export default {

    updatePersonal(request) {
        return api.post(`${url}/personal`, request);
    },

    updateAccount(request) {
        return api.post(`${url}/account`, request);
    },

    updateAvatar(request) {
        return api.post(`${url}/avatar`, request);
    },

    signOut() {
        return api.delete(url);
    },
}
