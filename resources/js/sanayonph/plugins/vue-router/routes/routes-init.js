import routes from './___.js';

export default [
    {
        path     : '/xx-demo',
        name     : 'demo',
        component: routes.load('Demo'),
        meta     : {
            rules: routes.rules.anyone
        }
    }
];
