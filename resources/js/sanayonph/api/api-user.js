import api from './___.js';
const  url = '/api/user';

export default {

    updatePersonal(request) {
        return api.post(`${url}/personal`, request);
    },

    updateAccount(request) {
        return api.post(`${url}/account`, request);
    },

    updateUserAvatar(request) {
        return api.post(`${url}/user-avatar`, request);
    },

    updateStoreAvatar(request) {
        return api.post(`${url}/store-avatar`, request);
    },

    signOut() {
        return api.delete(url);
    },
}
