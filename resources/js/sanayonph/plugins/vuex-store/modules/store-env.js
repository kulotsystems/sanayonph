/*
|--------------------------------------------------------------------------
| VUEX STORE MODULE: env
|--------------------------------------------------------------------------
| Reads environment variables
|
*/

export default {

    namespaced: true,

    state: {
        app: {
            name: process.env.MIX_APP_NAME
        },
        password: {
            minChars: process.env.MIX_PASSWORD_MIN_CHARS
        },
        otp: {
            validityTime   : process.env.MIX_OTP_VALIDITY_TIME,
            deleteTime     : process.env.MIX_OTP_DELETE_TIME,
            length         : process.env.MIX_OPT_LENGTH,
            allowedAttempts: process.env.MIX_OTP_ALLOWED_ATTEMPTS,
            allowedGenerate: process.env.MIX_MAXIMUM_OTPS_ALLOWED
        }
    },

    getters: {
        state: (state) => {
            return state
        }
    },

}
