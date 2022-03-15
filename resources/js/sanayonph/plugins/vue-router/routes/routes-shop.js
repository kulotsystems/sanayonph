import routes from './___.js';

export default [
    {
        path     : '/shop',
        name     : 'shop',
        component: routes.load('Shop/Shop'),
        meta     : {
            rules: routes.rules.anyone,
            title: 'Shop',
            chat : true
        }
    },
];
