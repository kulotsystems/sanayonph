<template>
    <v-app>
        <dialogs/>
        <toolbar-main hide-title custom-back="Back to Sign In" @onBack="$emit('onBack', $event)"></toolbar-main>
        <v-main class="yellow">
            <bg-sign-up/>
            <v-container class="pa-0 pa-sm-4 pt-sm-4">
                <v-row justify="center">
                    <v-col cols="12" md="8" lg="6" class="pa-5">
                        <v-card>
                            <v-card-text>
                                <!-- INTRO -->
                                <div class="pt-2">
                                    <h2 class="primary--text font-weight-bold">Password Reset</h2>
                                    <p class="primary--text mb-0 mt-2">
                                        Enter your e-mail, mobile number, or {{ $store.getters['env/state'].app.name }} username that you used
                                        to register. We will send you a verification code to reset your password.
                                    </p>
                                </div>

                                <v-stepper v-model="config.step" class="pb-4 pa-0" vertical flat>

                            <!-- STEP 1: Find Account -->
                            <v-stepper-step :complete="config.step > 1" step="1" class="px-0">
                                <span class="primary--text"><!--<b>STEP 1</b>: -->Find Account</span>
                            </v-stepper-step>
                            <v-stepper-content step="1" class="ma-0 pr-0">
                                <v-form ref="checkUsernameForm" class="py-1" @submit.prevent="checkUsername">
                                    <v-row dense>
                                        <v-col cols="12">
                                            <v-text-field
                                                label="E-mail / Mobile / Username"
                                                v-model="request.username"
                                                :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response, 'username')]"
                                                @focus="$store.dispatch('form/reset', [response, 'username']).then(r => response)"
                                                @keyup="$store.dispatch('form/reset', [response, 'username']).then(r => response)"
                                                outlined
                                            />
                                        </v-col>
                                    </v-row>

                                    <!-- Control Buttons -->
                                    <v-row dense>
                                        <v-col cols="12">
                                            <button-action type="submit" label="NEXT" icon="arrow_downward" large :loading="config.btn_check_username.loading" @click="checkUsername"/>
                                        </v-col>
                                    </v-row>

                                </v-form>
                            </v-stepper-content>


                            <!-- STEP 2: Choose Verification Method -->
                            <v-stepper-step :complete="config.step > 2" step="2" class="px-0">
                                <span class="primary--text"><!--<b>STEP 2</b>: -->Choose Verification Method</span>
                            </v-stepper-step>
                            <v-stepper-content step="2" class="ma-0 pr-0">
                                <v-row dense>
                                    <v-col cols="12">
                                        <v-radio-group v-model="config.resetMethod" class="ml-2">
                                            <v-radio v-for="(resetMethod, key, index) in config.resetMethods" :key="key" :value="key" v-if="resetMethod.value">
                                                <template v-slot:label>
                                                    <div>
                                                        {{ resetMethod.action }} verification code to your registered {{ resetMethod.label }}:
                                                        <b class="primary--text">{{ resetMethod.value }}</b>.
                                                    </div>
                                                </template>
                                            </v-radio>
                                        </v-radio-group>

                                        <!-- Control Buttons -->
                                        <v-row dense>
                                            <v-col cols="12">
                                                <button-action color="light" label="BACK" icon="arrow_upward"   large :disabled="config.btn_send_otp.loading" @click="config.step = 1"/>
                                                <button-action type="submit" label="NEXT" icon="arrow_downward" large :loading="config.btn_send_otp.loading"  @click="sendOtp"/>
                                            </v-col>
                                        </v-row>
                                    </v-col>
                                </v-row>
                            </v-stepper-content>


                            <!-- STEP 3: Enter Verification Code -->
                            <v-stepper-step :complete="config.step > 3" step="3" class="px-0">
                                <span class="primary--text"><!--<b>STEP 3:</b> -->Enter Verification Code</span>
                            </v-stepper-step>
                            <v-stepper-content step="3" class="ma-0 pr-0">
                                <v-form ref="otpForm" class="py-1" @submit.prevent="verifyOtp">
                                    <v-row dense class="mb-1">
                                        <!-- OTP -->
                                        <v-col cols="12">
                                            <p class="primary--text text-subtitle-2" v-if="config.resetMethod != null">
                                                Please enter the verification code we sent you to {{ config.resetMethods[config.resetMethod].value }}.
                                            </p>
                                            <v-text-field
                                                label="Verification Code"
                                                v-model="request.otp"
                                                type="number"
                                                :rules="[$store.state.form.rules.required, $store.state.form.rules.min_chars(request.otp, $store.state.env.otp.length, 'digits'), $store.state.form.rules.feedback(response,'otp')]"
                                                @keyup.enter="verifyOtp"
                                                @keyup="$store.dispatch('form/reset', [response, 'otp']).then(r => response)"
                                                outlined
                                            />
                                            <!--<v-otp-input
                                                :length="$store.getters['env/state'].otp.length"
                                                v-model="request.otp"
                                                :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'otp')]"
                                                @change="$store.dispatch('form/reset', [response, 'otp']).then(r => response)"
                                            />-->
                                        </v-col>
                                    </v-row>

                                    <!-- Control Buttons -->
                                    <v-row dense>
                                        <v-col cols="12">
                                            <button-action color="light" label="BACK" icon="arrow_upward"   large :disabled="config.btn_verify_otp.loading" @click="config.step = 2"/>
                                            <button-action type="submit" label="NEXT" icon="arrow_downward" large :loading="config.btn_verify_otp.loading"  @click="verifyOtp"/>
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-stepper-content>


                            <!-- STEP 4: Create New Password -->
                            <v-stepper-step :complete="config.step > 4" step="4" class="px-0">
                                <span class="primary--text"><!--<b>STEP 4:</b> -->Create New Password</span>
                            </v-stepper-step>
                            <v-stepper-content step="4" class="ma-0 pr-0">
                                <v-form ref="changePasswordForm" class="py-1" @submit.prevent="changePassword">
                                    <v-row dense>
                                        <!-- New Password -->
                                        <v-col cols="12" sm="6">
                                            <v-text-field
                                                label="New Password"
                                                v-model="request.password"
                                                :rules="[
                                                    $store.state.form.rules.required,
                                                    $store.state.form.rules.min_chars(request.password, $store.state.env.password.minChars),
                                                    $store.state.form.rules.feedback(response,'password')
                                                ]"
                                                @keyup="$store.dispatch('form/reset', [response, 'password']).then(r => response)"
                                                :append-icon="config.password.show ? 'visibility' : 'visibility_off'"
                                                :type="config.password.show ? 'text' : 'password'"
                                                @click:append="config.password.show = !config.password.show"
                                                outlined
                                            />
                                        </v-col>
                                        <!-- Password Confirmation -->
                                        <v-col cols="12" sm="6">
                                            <v-text-field
                                                label="Confirm New Password"
                                                v-model="request.password_confirmation"
                                                :rules="[
                                                    $store.state.form.rules.required,
                                                    $store.state.form.rules.min_chars(request.password_confirmation, $store.state.env.password.minChars),
                                                    $store.state.form.rules.password.match(request.password, request.password_confirmation),
                                                    $store.state.form.rules.feedback(response,'password')
                                                ]"
                                                @keyup="$store.dispatch('form/reset', [response, 'password']).then(r => response)"
                                                :type="config.password.show ? 'text' : 'password'"
                                                outlined
                                            />
                                        </v-col>
                                    </v-row>

                                    <!-- Control Buttons -->
                                    <v-row dense>
                                        <v-col cols="12">
                                            <button-action color="light" label="BACK" icon="arrow_upward" large :disabled="config.btn_change_password.loading" @click="config.step = 3"/>
                                            <button-action type="submit" label="CONFIRM" icon="done"      large :loading="config.btn_change_password.loading"  @click="changePassword"/>
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-stepper-content>
                        </v-stepper>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    import api_auth from '../../api/api-auth.js';

    export default {
        name: 'AuthResetPassword',
        components: {
            'bg-sign-up'   : () => import('../../components/backgrounds/BackgroundSignUp.vue'),
            'dialogs'      : () => import('../../components/dialogs/Dialogs.vue'),
            'toolbar-main' : () => import('../../components/toolbars/ToolbarMain.vue'),
            'button-action': () => import('../../components/buttons/ButtonAction.vue'),
        },
        data() {
            return {
                config: {
                    step: 1,
                    btn_check_username: {
                        loading: false
                    },
                    btn_send_otp: {
                        loading: false
                    },
                    btn_verify_otp: {
                        loading: false
                    },
                    btn_change_password: {
                        loading: false
                    },
                    password: {
                        show: false
                    },
                    resetMethods: {
                        email : {
                            label  : 'e-mail address',
                            value  : null,
                            action : 'E-mail'
                        },
                        mobile: {
                            label  : 'mobile number',
                            value  : null,
                            action : 'Text'
                        }
                    },
                    resetMethod: null
                },
                request: {
                    username: '',
                    otp     : '',
                    token   : '',
                    password: '',
                    password_confirmation: '',
                },
                response: {
                    message: '',
                    errors : {}
                }
            }
        },
        computed: {},
        methods: {
            checkUsername()
            {
                let checkUsernameForm = this.$refs.checkUsernameForm;
                if(!this.config.btn_check_username.loading && checkUsernameForm.validate()) {
                    this.response.message = '';
                    this.response.errors  = {};
                    this.config.btn_check_username.loading = true;
                    this.config.resetMethods.email.value  = null;
                    this.config.resetMethods.mobile.value = null;
                    this.config.resetMethod = null;
                    api_auth.reset.checkUsername({
                        username: this.request.username
                    }).then(response => {
                        if(!response) return;

                        if(response.data.credentials.email !== null) {
                            this.config.resetMethods.email.value = response.data.credentials.email;
                            if(!this.config.resetMethod)
                                this.config.resetMethod = 'email';
                        }
                        if(response.data.credentials.mobile !== null) {
                            this.config.resetMethods.mobile.value = response.data.credentials.mobile;
                            if(!this.config.resetMethod)
                                this.config.resetMethod = 'mobile';
                        }
                        this.config.step = 2;
                        this.config.btn_check_username.loading = false;
                    }).catch(errors => {
                        this.config.btn_check_username.loading = false;
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                        checkUsernameForm.validate();
                    });
                }
            },

            sendOtp()
            {
                if(!this.config.btn_send_otp.loading) {
                    this.response.message = '';
                    this.response.errors  = {};
                    this.config.btn_send_otp.loading = true;

                    api_auth.reset.otpSend({
                        username: this.request.username,
                        mode    : this.config.resetMethod
                    }).then(response => {
                        if(!response) return;

                        if(response.data.message != null) {
                            this.response.message = response.data.message;
                            this.$store.commit('dialog/error/show', this.response);
                        }
                        else {
                            if(response.data.otp_sent)
                                this.config.step = 3;
                        }
                        this.config.btn_send_otp.loading = false;
                    }).catch(errors => {
                        this.config.btn_send_otp.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                    });
                }
            },


            verifyOtp()
            {
                let otpForm = this.$refs.otpForm;
                if(!this.config.btn_verify_otp.loading && otpForm.validate()) {
                    this.response.message = '';
                    this.response.errors  = {};
                    this.config.btn_verify_otp.loading = true;

                    api_auth.reset.otpVerify({
                        username: this.request.username,
                        otp     : this.request.otp
                    }).then(response => {
                        if(!response) return;

                        if(response.data.message != null) {
                            this.response.errors.otp = [response.data.message];
                            this.$store.commit('dialog/error/show', this.response);
                        }
                        else {
                            if(response.data.token != null) {
                                this.request.token = response.data.token;
                                this.config.step = 4;
                            }
                        }
                        this.config.btn_verify_otp.loading = false;
                    }).catch(errors => {
                        this.config.btn_verify_otp.loading = false;
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                        otpForm.validate();
                    })
                }
            },

            changePassword()
            {
                let changePasswordForm = this.$refs.changePasswordForm;
                if(!this.config.btn_change_password.loading && changePasswordForm.validate()) {
                    this.response.message = '';
                    this.response.errors = {};
                    this.config.btn_change_password.loading = true;

                    api_auth.reset.changePassword(this.request).then(response => {
                        if(!response) return;

                        if(response.data.message != null) {
                            this.response.message = response.data.message;
                            this.$store.commit('dialog/error/show', this.response);
                        }
                        else {
                            if(response.data.password_changed) {
                                this.config.step = 5;
                                this.$store.commit('dialog/message/show', {
                                    title  : 'Password Changed',
                                    prompt : 'You have successfully changed your password.',
                                    okIcon : 'arrow_forward',
                                    okLabel: 'Sign In',
                                    callback: {
                                        async : true,
                                        action: () => {
                                            window.open(this.$router.history.base, '_self');
                                        }
                                    },
                                    persistent: true
                                });
                            }
                        }
                        this.config.btn_change_password.loading = false;
                    }).catch(errors => {
                        this.config.btn_change_password.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                        changePasswordForm.validate();
                    });
                }
            },
        }
    }
</script>

<style scoped>

</style>
