import routes from './___.js'

export default [
    {
        path     : '/:username',
        name     : 'profile',
        component: routes.load('Profile/Profile'),
        meta     : {
            rules: routes.rules.anyone,
            title: 'Profile',
            chat : true
        }
    },
    {
        path     : '/:username/edit/:tab?',
        name     : 'profile-edit',
        component: routes.load('Profile/Self/ProfileSelfEdit'),
        meta     : {
            rules: routes.rules.user,
            back : 'profile',
            title: 'Edit Profile',
            defaultTab: 'personal'
        }
    },
    {
        path     : '/:username/delivery-addresses',
        name     : 'profile-delivery-addresses',
        component: routes.load('Profile/Self/ProfileSelfDeliveryAddresses'),
        meta     : {
            rules: routes.rules.user,
            back : 'profile',
            title: 'Delivery Addresses'
        }
    },
    //=====|||
            {
                path     : '/:username/delivery-addresses/add',
                name     : 'profile-delivery-addresses-add',
                component: routes.load('Profile/Self/ProfileSelfDeliveryAddressesModify'),
                meta     : {
                    rules: routes.rules.user,
                    back : 'profile-delivery-addresses',
                    title: 'Add Delivery Address'
                }
            },
            {
                path     : '/:username/delivery-addresses/edit/:delivery_address',
                name     : 'profile-delivery-addresses-edit',
                component: routes.load('Profile/Self/ProfileSelfDeliveryAddressesModify'),
                meta     : {
                    rules: routes.rules.user,
                    back : 'profile-delivery-addresses',
                    title: 'Edit Delivery Address'
                }
            },

    {
        path     : '/:username/orders',
        name     : 'profile-orders',
        component: routes.load('Profile/Self/ProfileSelfOrdersOLD'),
        meta     : {
            rules: routes.rules.user,
            back : 'profile',
            title: 'My Orders'
        }
    },
    //=====|||
            {
                path     : '/:username/orders/:order',
                name     : 'profile-orders-details',
                component: routes.load('Profile/Self/ProfileSelfOrdersOLDDetails'),
                meta     : {
                    rules: routes.rules.user,
                    back : 'profile-orders',
                    title: 'Order Details'
                }
            },


    {
        path     : '/:username/orders-dev/:tab?',
        name     : 'profile-orders',
        component: routes.load('Profile/Self/ProfileSelfOrders'),
        meta     : {
            rules     : routes.rules.user,
            back      : 'profile',
            title     : 'My Orders',
            defaultTab: 'payment'
        }
    },
    //=====|||
            {
                path     : '/:username/orders-dev/:order',
                name     : 'profile-orders-details',
                component: routes.load('Profile/Self/ProfileSelfOrdersDetails'),
                meta     : {
                    rules: routes.rules.user,
                    back : 'profile-orders',
                    title: 'Order Details'
                }
            },



    {
        path     : '/:username/notifications',
        name     : 'profile-notifications',
        component: routes.load('Profile/Self/ProfileSelfNotifications'),
        meta     : {
            rules: routes.rules.user,
            back : 'profile',
            title: 'Notifications'
        }
    },
    {
        path     : '/:username/favorites',
        name     : 'profile-favorites',
        component: routes.load('Profile/Self/ProfileSelfFavorites'),
        meta     : {
            rules: routes.rules.user,
            back : 'profile',
            title: 'Favorites'
        }
    },
    {
        path     : '/:username/recent-views',
        name     : 'profile-recent-views',
        component: routes.load('Profile/Self/ProfileSelfRecentViews'),
        meta     : {
            rules: routes.rules.user,
            back : 'profile',
            title: 'Recently Viewed'
        }
    },
    {
        path     : '/:username/faqs',
        name     : 'profile-faqs',
        component: routes.load('Profile/Self/ProfileSelfFAQs'),
        meta     : {
            rules: routes.rules.user,
            back : 'profile',
            title: 'FAQs'
        }
    },


    // LAST ROUTE !! IMPORTANT
    {
        path     : '/:username/:store',
        name     : 'profile-store',
        component: routes.load('Profile/ProfileStore'),
        meta     : {
            rules: routes.rules.anyone,
            back : 'profile',
            title: 'Store'
        }
    },
    //=====|||
            {
                path     : '/:username/:store/categories',
                name     : 'profile-store-categories',
                component: routes.load('Profile/ProfileStoreCategories'),
                meta     : {
                    rules: routes.rules.anyone,
                    back : 'profile-store',
                    title: 'Categories'
                }
            },
            //=====|||
                    {
                        path     : '/:username/:store/categories/add',
                        name     : 'profile-store-categories-add',
                        component: routes.load('Profile/Self/ProfileSelfStoreCategoriesModify'),
                        meta     : {
                            rules: routes.rules.user,
                            back : 'profile-store-categories',
                            title: 'Add Category'
                        }
                    },
                    {
                        path     : '/:username/:store/categories/edit/:category',
                        name     : 'profile-store-categories-edit',
                        component: routes.load('Profile/Self/ProfileSelfStoreCategoriesModify'),
                        meta     : {
                            rules: routes.rules.user,
                            back : 'profile-store-categories',
                            title: 'Edit Category'
                        }
                    },

            {
                path     : '/:username/:store/products',
                name     : 'profile-store-products',
                component: routes.load('Profile/ProfileStoreProducts'),
                meta     : {
                    rules: routes.rules.anyone,
                    back : 'profile-store',
                    title: 'Products',
                    chat : true
                }
            },
            //=====|||
                    {
                        path     : '/:username/:store/products/add/:tab?',
                        name     : 'profile-store-products-add',
                        component: routes.load('Profile/Self/ProfileSelfStoreProductsModify'),
                        meta     : {
                            rules     : routes.rules.user,
                            back      : 'profile-store-products',
                            title     : 'Add Product',
                            defaultTab: 'general'
                        }
                    },
                    {
                        path     : '/:username/:store/products/edit/:product/:tab?',
                        name     : 'profile-store-products-edit',
                        component: routes.load('Profile/Self/ProfileSelfStoreProductsModify'),
                        meta     : {
                            rules     : routes.rules.user,
                            back      : 'profile-store-products',
                            title     : 'Edit Product',
                            defaultTab: 'general'
                        }
                    },
                    {
                        path     : '/:username/:store/products/:product',
                        name     : 'profile-store-products-show',
                        component: routes.load('Profile/ProfileStoreProductsDetails'),
                        meta     : {
                            rules     : routes.rules.anyone,
                            back      : 'profile-store-products',
                            title     : 'Product Details',
                            chat      : true
                        }
                    },


            {
                path     : '/:username/:store/orders',
                name     : 'profile-store-orders',
                component: routes.load('Profile/Self/ProfileSelfStoreOrdersOLD'),
                meta     : {
                    rules: routes.rules.user,
                    back : 'profile-store',
                    title: 'Store Orders'
                }
            },
            //=====|||
                    {
                        path     : '/:username/:store/orders/:order',
                        name     : 'profile-store-orders-details',
                        component: routes.load('Profile/Self/ProfileSelfStoreOrdersOLDDetails'),
                        meta     : {
                            rules: routes.rules.user,
                            back : 'profile-store-orders',
                            title: 'Store Order Details'
                        }
                    },


            {
                path     : '/:username/:store/orders-dev/:tab?',
                name     : 'profile-store-orders',
                component: routes.load('Profile/Self/ProfileSelfStoreOrders'),
                meta     : {
                    rules     : routes.rules.user,
                    back      : 'profile-store',
                    title     : 'Store Orders',
                    defaultTab: 'payment'
                }
            },
                    //=====|||
                    {
                        path     : '/:username/:store/orders-dev/:order',
                        name     : 'profile-store-orders-details',
                        component: routes.load('Profile/Self/ProfileSelfStoreOrdersDetails'),
                        meta     : {
                            rules: routes.rules.user,
                            back : 'profile-store-orders',
                            title: 'Store Order Details'
                        }
                    },

];
