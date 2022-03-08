/*
|--------------------------------------------------------------------------
| VUEX STORE MODULE: form
|--------------------------------------------------------------------------
| Used in validating forms before and after submission
|
*/


export default {

    namespaced: true,

    state: {
        rules: {
            required: v => !!v || 'This field is required.',
            chars: (v, m, a) => {
                if(a == null)
                    a = 'characters';
                return v != null ? v.toString().length === m || 'Must be ' + m.toString() + ' ' + a + '.' : true;
            },
            min_chars: (v, m, a) => {
                if(a == null)
                    a = 'characters';
                return v.toString().length >= m || 'Minimum ' + a + ': ' + m.toString() + '.';
            },
            max_chars: (v, m, a) => {
                if(a == null)
                    a = 'characters';
                return v != null ? v.toString().length <= m || 'Maximum ' + a + ': ' + m.toString() + '.' : true;
            },
            min_value: (v, m, a) => {
                if(a == null)
                    a = 'value';
                return parseInt(v) >= m || 'Minimum ' + a + ': ' + m.toString() + '.';
            },
            max_value: (v, m, a) => {
                if(a == null)
                    a = 'value';
                return parseInt(v) <= m || 'Maximum ' + a + ': ' + m.toString() + '.';
            },
            email: {
                valid: v => {
                    const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    return pattern.test(v) || 'Invalid e-mail address.'
                }
            },
            password: {
                match: (v1, v2) => {
                    return v1 === v2 || 'Passwords must match'
                }
            },
            feedback: (response, field) => {
                return !response.errors[field] || response.errors[field][0];
            }
        }
    },

    actions: {

        /**
         * Reset form field
         *
         * @param payload - Array: [response, field]
         */
        reset({ }, payload) {
            if(payload[0].errors[payload[1]])
                delete payload[0].errors[payload[1]];
            if(Object.keys(payload[0].errors).length <= 0)
                payload[0].message = '';

            return payload[0];
        }

    }
}
