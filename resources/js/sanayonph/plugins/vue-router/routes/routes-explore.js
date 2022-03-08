import routes from './___.js';

export default [
    {
        path     : '/explore',
        name     : 'explore',
        component: routes.load('Explore/Explore'),
        meta     : {
            rules: routes.rules.user,
            title: 'Explore'
        }
    },
];
