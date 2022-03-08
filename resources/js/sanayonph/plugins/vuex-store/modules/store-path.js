/*
|--------------------------------------------------------------------------
| VUEX STORE MODULE: path
|--------------------------------------------------------------------------
| Configurable paths for resources
|
*/

export default {

    namespaced: true,

    state: {
        images      : '/images',
        imageUploads: '/uploads/images',
    },

    getters: {
        placeholder: (state) => {
            return state.images + '/zzz.png';
        },
        productImg: (state) => {
            return {
                '300': state.imageUploads + '/products/300',
                '128': state.imageUploads + '/products/128',
                '064': state.imageUploads + '/products/064',
            }
        },
        userAvatar: (state) => {
            return {
                '256': state.imageUploads + '/avatars/256',
                '128': state.imageUploads + '/avatars/128',
                '064': state.imageUploads + '/avatars/064',
            }
        },
        paymentScreenshot: (state) => {
            return state.imageUploads + '/screenshots';
        },
        defaultAvatar: (state) => {
            return state.images + '/default-white.png';
        },
        defaultProduct: (state) => {
            return state.images + '/product.png';
        },
        signInBGMobile: (state) => {
            return state.images + '/sign-in-bg-mobile-v2.png';
        },
        signInBGDesktop: (state) => {
            return state.images + '/sign-in-bg-desktop-v2.png';
        },
        signUpBGMobile: (state) => {
            return state.images + '/sign-up-bg-mobile-v2.png';
        },
        signUpBGDesktop: (state) => {
            return state.images + '/sign-up-bg-desktop-v2.png';
        },
        yellowBGMobile: (state) => {
            return state.images + '/yellow-bg-mobile-v1.png';
        },
        yellowBGDesktop: (state) => {
            return state.images + '/yellow-bg-desktop-v1.png';
        },
        secondaryBGMobile: (state) => {
            return state.images + '/secondary-bg-mobile-v1.png';
        },
        secondaryBGDesktop: (state) => {
            return state.images + '/secondary-bg-desktop-v1.png';
        }
    }

}
