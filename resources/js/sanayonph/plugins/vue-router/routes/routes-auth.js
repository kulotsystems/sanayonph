import routes from './___.js';

export default [
    {
        path     : '/',
        name     : 'sign-in',
        component: routes.load('Auth/AuthSignIn'),
        meta     : {
            rules: routes.rules.guest,
            title: 'Sign In',
        },
    },
    {
        path     : '/sign-up',
        name     : 'sign-up',
        component: routes.load('Auth/AuthSignUp'),
        meta     : {
            rules: routes.rules.guest,
            title: 'Sign Up',
            back : 'sign-in'
        }
    },
    {
        path     : '/reset-password',
        name     : 'reset-password',
        component: routes.load('Auth/AuthResetPassword'),
        meta     : {
            rules: routes.rules.guest,
            title: 'Password Reset',
            back : 'sign-in'
        }
    },
];
