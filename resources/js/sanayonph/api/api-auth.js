import api from './___.js';
const  url = '/api/auth';

export default {

    session() {
        return api.get('/sanctum/csrf-cookie');
    },

    signUp: {
        otpSend(request) {
            return api.post(`${url}/sign-up/otp-send`, request);
        },

        otpVerify(request) {
            return api.post(`${url}/sign-up/otp-verify`, request);
        }
    },

    signIn: {
        proceed(request) {
            return api.post(`${url}/sign-in`, request);
        },

        user() {
            return api.get(`${url}/sign-in/user`);
        }
    },

    reset: {
        checkUsername(request) {
            return api.post(`${url}/reset/check-username`, request);
        },

        otpSend(request) {
            return api.post(`${url}/reset/otp-send`, request);
        },

        otpVerify(request) {
            return api.post(`${url}/reset/otp-verify`, request);
        },

        changePassword(request) {
            return api.post(`${url}/reset/change-password`, request);
        },
    },

}
