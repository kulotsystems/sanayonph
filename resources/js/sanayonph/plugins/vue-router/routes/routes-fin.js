import routes from './___.js';

export default [
    {
        path     : '*',
        name     : '404-not-found',
        component: routes.load('Error404NotFound'),
        meta     : {
            rules: routes.rules.anyone,
            back : 'profile',
        }
    },
];
