export default {

    load: (view) => {
        return() => import(`../../../views/${view}.vue`);
    },

    rules: {
        guest: {
            guest: {
                accept  : true,
            },
            user : {
                accept  : false,
                redirect: '/shop'
            }
        },
        user: {
            guest: {
                accept  : false,
                redirect: '/'
            },
            user : {
                accept  : true,
            }
        },
        anyone: {
            guest: {
                accept  : true,
            },
            user : {
                accept  : true,
            }
        }
    }

};
