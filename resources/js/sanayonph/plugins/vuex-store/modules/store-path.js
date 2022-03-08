/*
|--------------------------------------------------------------------------
| VUEX STORE MODULE: path
|--------------------------------------------------------------------------
| Configurable paths for resources
|
*/

import favIcon            from '../../../assets/img/favicon.png';
import signInBGMobile     from '../../../assets/img/bg/sign-in-bg-mobile-v2.png';
import signInBGDesktop    from '../../../assets/img/bg/sign-in-bg-desktop-v2.png';
import signUpBGMobile     from '../../../assets/img/bg/sign-up-bg-mobile-v2.png';
import signUpBGDesktop    from '../../../assets/img/bg/sign-up-bg-desktop-v2.png';
import yellowBGMobile     from '../../../assets/img/bg/yellow-bg-mobile-v1.png';
import yellowBGDesktop    from '../../../assets/img/bg/yellow-bg-desktop-v1.png';
import secondaryBGMobile  from '../../../assets/img/bg/secondary-bg-mobile-v1.png';
import secondaryBGDesktop from '../../../assets/img/bg/secondary-bg-desktop-v1.png';
import defaultAvatar      from '../../../assets/img/default-white.png';
import defaultProduct     from '../../../assets/img/product.png';

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
        favIcon: (state) => {
            return favIcon;
        },
        defaultAvatar: (state) => {
            return defaultAvatar;
        },
        defaultProduct: (state) => {
            return defaultProduct;
        },
        signInBGMobile: (state) => {
            return signInBGMobile;
        },
        signInBGDesktop: (state) => {
            return signInBGDesktop;
        },
        signUpBGMobile: (state) => {
            return signUpBGMobile;
        },
        signUpBGDesktop: (state) => {
            return signUpBGDesktop;
        },
        yellowBGMobile: (state) => {
            return yellowBGMobile;
        },
        yellowBGDesktop: (state) => {
            return yellowBGDesktop;
        },
        secondaryBGMobile: (state) => {
            return secondaryBGMobile;
        },
        secondaryBGDesktop: (state) => {
            return secondaryBGDesktop;
        }
    }

}
