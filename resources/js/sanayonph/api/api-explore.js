import api from './___.js';
const  url = '/api/explore';

export default {

    query(query) {
        return api.get(url, {params: query});
    }

}
