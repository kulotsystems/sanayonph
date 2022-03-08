import api from './___.js';
const  url = '/api/shop';

export default {

    stores() {
        return api.get(`${url}/stores`);
    }

}
