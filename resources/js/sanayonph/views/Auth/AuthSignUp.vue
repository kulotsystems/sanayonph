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
                                    <h2 class="primary--text font-weight-bold">Sign Up</h2>
                                    <p class="primary--text mb-0 mt-2">
                                        Sanayon ang sadiri. Support local. Shop local.
                                    </p>
                                </div>

                                <v-stepper v-model="config.step" class="pb-4 pa-0" vertical flat>
                                    <!-- STEP 1: Enter Account Information -->
                                    <v-stepper-step :complete="config.step > 1" step="1" class="px-0">
                                        <span class="primary--text"><!--<b>STEP 1:</b> -->Enter Account Information</span>
                                    </v-stepper-step>
                                    <v-stepper-content step="1" class="ma-0 pr-0">
                                        <v-form ref="signUpForm" class="py-1" @submit.prevent="sendOtp">

                                            <v-row dense>
                                                <v-col cols="12">
                                                    <!-- E-mail or Mobile Number -->
                                                    <v-text-field
                                                        label="E-mail or Mobile Number"
                                                        v-model="request.email_or_mobile"
                                                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response, 'email_or_mobile')]"
                                                        @keyup="$store.dispatch('form/reset', [response, 'email_or_mobile']).then(r => response)"
                                                        outlined
                                                    />
                                                </v-col>
                                            </v-row>

                                            <v-row dense>
                                                <!-- First Name -->
                                                <v-col cols="12" sm="6">
                                                    <v-text-field
                                                        label="First Name"
                                                        v-model="request.first_name"
                                                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'first_name')]"
                                                        @keyup="$store.dispatch('form/reset', [response, 'first_name']).then(r => response)"
                                                        placeholder="ex: JUAN"
                                                        outlined
                                                        dense
                                                    />
                                                </v-col>
                                                <!-- Middle Name -->
                                                <v-col cols="12" sm="6">
                                                    <v-text-field
                                                        label="Middle Name"
                                                        v-model="request.middle_name"
                                                        :rules="[$store.state.form.rules.feedback(response,'middle_name')]"
                                                        @keyup="$store.dispatch('form/reset', [response, 'middle_name']).then(r => response)"
                                                        placeholder="ex: REYES"
                                                        hint="(Optional)"
                                                        outlined
                                                        dense
                                                    />
                                                </v-col>
                                                <!-- Last Name -->
                                                <v-col cols="12" sm="6">
                                                    <v-text-field
                                                        label="Last Name"
                                                        v-model="request.last_name"
                                                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'last_name')]"
                                                        @keyup="$store.dispatch('form/reset', [response, 'last_name']).then(r => response)"
                                                        placeholder="ex: DELA CRUZ"
                                                        outlined
                                                        dense
                                                    />
                                                </v-col>
                                                <!-- Name Suffix -->
                                                <v-col cols="12" sm="6">
                                                    <v-text-field
                                                        label="Name Suffix"
                                                        v-model="request.name_suffix"
                                                        :rules="[$store.state.form.rules.feedback(response,'name_suffix')]"
                                                        @keyup="$store.dispatch('form/reset', [response, 'name_suffix']).then(r => response)"
                                                        placeholder="ex: JR."
                                                        hint="(Optional)"
                                                        outlined
                                                        dense
                                                    />
                                                </v-col>
                                            </v-row>

                                            <v-row dense>
                                                <!-- Gender -->
                                                <v-col cols="5">
                                                    <v-select
                                                        label="Gender"
                                                        v-model="request.gender"
                                                        :items="['Male', 'Female']"
                                                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'gender')]"
                                                        @change="$store.dispatch('form/reset', [response, 'gender']).then(r => response)"
                                                        outlined
                                                        dense
                                                    />
                                                </v-col>

                                                <!-- Date of Birth -->
                                                <v-col cols="7">
                                                    <v-dialog
                                                        ref="dob_dialog"
                                                        v-model="config.date_of_birth.modal"
                                                        :return-value.sync="request.date_of_birth"
                                                        persistent
                                                        width="290px"
                                                    >
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-text-field
                                                                label="Date of Birth"
                                                                v-bind="attrs"
                                                                v-on="on"
                                                                :value="formattedDateOfBirth"
                                                                :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'date_of_birth')]"
                                                                @blur="$store.dispatch('form/reset', [response, 'date_of_birth']).then(r => response)"
                                                                readonly
                                                                outlined
                                                                dense
                                                            />
                                                        </template>
                                                        <v-date-picker
                                                            v-model="request.date_of_birth"
                                                            scrollable
                                                        >
                                                            <v-spacer></v-spacer>
                                                            <v-btn text color="primary" @click="config.date_of_birth.modal = false">CANCEL</v-btn>
                                                            <v-btn text color="primary" @click="$refs.dob_dialog.save(request.date_of_birth)">OK</v-btn>
                                                        </v-date-picker>
                                                    </v-dialog>
                                                </v-col>
                                            </v-row>

                                            <v-row dense>
                                                <!-- Username -->
                                                <v-col cols="12 mt-3">
                                                    <v-text-field
                                                        label="Username"
                                                        v-model="request.username"
                                                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'username')]"
                                                        @keyup="$store.dispatch('form/reset', [response, 'username']).then(r => response)"
                                                        outlined
                                                    />
                                                </v-col>
                                            </v-row>

                                            <v-row dense>
                                                <!-- Password -->
                                                <v-col cols="12" sm="6">
                                                    <v-text-field
                                                        label="Password"
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
                                                        dense
                                                    />
                                                </v-col>
                                                <!-- Password Confirmation -->
                                                <v-col cols="12" sm="6">
                                                    <v-text-field
                                                        label="Confirm Password"
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
                                                        dense
                                                    />
                                                </v-col>
                                            </v-row>


                                            <v-row dense>
                                                <!-- Checkbox -->
                                                <v-col cols="12" class="pl-3 pr-3">
                                                    <v-checkbox
                                                        v-model="config.agree"
                                                        :rules="[
                                                            $store.state.form.rules.required
                                                        ]"
                                                        class="ma-0"
                                                    >
                                                        <template v-slot:label>
                                                            <div>
                                                                I agree to {{ $store.getters['env/state'].app.name }} <u><privacy-policy/></u> and <u><terms-and-conditions/></u>.
                                                            </div>
                                                        </template>
                                                    </v-checkbox>
                                                </v-col>
                                            </v-row>

                                            <!-- Control Buttons -->
                                            <v-row dense>
                                                <v-col cols="12">
                                                    <button-action type="submit" label="NEXT" icon="arrow_downward" large :loading="config.btn_send_otp.loading" @click="sendOtp"/>
                                                </v-col>
                                            </v-row>
                                        </v-form>
                                    </v-stepper-content>


                                    <!-- STEP 2: Verify Registration -->
                                    <v-stepper-step :complete="config.step > 2" step="2" class="px-0">
                                        <span class="primary--text"><!--<b>STEP 3:</b> -->Verify Registration</span>
                                    </v-stepper-step>
                                    <v-stepper-content step="2" class="ma-0 pr-0">
                                        <v-form ref="otpForm" class="py-1" @submit.prevent="verifyOtp">
                                            <v-row dense class="mb-1">
                                                <!-- OTP -->
                                                <v-col cols="12">
                                                    <p class="primary--text text-subtitle-2">
                                                        Please enter the verification code we sent you to {{ response.identifier }}.
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
                                                    <button-action color="light" label="BACK" icon="arrow_upward" large :disabled="config.btn_verify_otp.loading" @click="config.step = 1"/>
                                                    <button-action type="submit" label="SIGN UP" icon="done"      large :loading="config.btn_verify_otp.loading"  @click="verifyOtp"/>
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
    import format   from 'date-fns/format';

    export default {
        name: 'AuthSignUp',
        components: {
            'bg-sign-up'   : () => import('../../components/backgrounds/BackgroundSignUp.vue'),
            'dialogs'      : () => import('../../components/dialogs/Dialogs.vue'),
            'toolbar-main' : () => import('../../components/toolbars/ToolbarMain.vue'),
            'button-action': () => import('../../components/buttons/ButtonAction.vue'),
            'privacy-policy'      : () => import('../../components/contracts/PrivacyPolicy.vue'),
            'terms-and-conditions': () => import('../../components/contracts/TermsAndConditions.vue')
        },
        data() {
            return {
                config: {
                    step : 1,
                    btn_send_otp: {
                        loading: false
                    },
                    btn_verify_otp: {
                        loading: false
                    },
                    date_of_birth: {
                        modal: false
                    },
                    password: {
                        show: false
                    },
                    agree: false
                },
                request: {
                    email_or_mobile : '',
                    otp             : '',
                    first_name      : '',
                    middle_name     : '',
                    last_name       : '',
                    name_suffix     : '',
                    gender          : '',
                    date_of_birth   : '',
                    username        : '',
                    password        : '',
                    password_confirmation: ''
                },
                response: {
                    message   : '',
                    errors    : {},
                    identifier: ''
                }
            }
        },
        computed: {
            formattedDateOfBirth() {
                return this.request.date_of_birth ? format(new Date(this.request.date_of_birth), 'MMMM d, yyy') : '';
            }
        },
        methods: {
            /****************************************************************************************************
             * METHOD: SEND OTP
             * Generate and send OTP through chosen sign up method.
             */
            sendOtp()
            {
                let signUpForm = this.$refs.signUpForm;
                if(!this.config.btn_send_otp.loading && signUpForm.validate()) {
                    this.response.message = '';
                    this.response.errors  = {};
                    this.config.btn_send_otp.loading = true;
                    this.request.username        = this.request.username.toLowerCase();
                    this.request.email_or_mobile = this.request.email_or_mobile.toLowerCase();

                    api_auth.signUp.otpSend(this.request).then(response => {
                        if(!response) return;

                        if(response.data.message != null) {
                            this.response.message = response.data.message;
                            this.$store.commit('dialog/error/show', this.response);
                        }
                        else {
                            this.response.identifier = response.data.identifier != null ? response.data.identifier : '';
                            this.config.step = 2;
                        }
                        this.config.btn_send_otp.loading = false;
                    }).catch(errors => {
                        this.config.btn_send_otp.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;

                        if(this.response.errors.email)
                            this.response.errors.email_or_mobile = this.response.errors.email;
                        if(this.response.errors.mobile)
                            this.response.errors.email_or_mobile = this.response.errors.mobile;
                        signUpForm.validate();
                    });
                }
            },


            /****************************************************************************************************
             * METHOD: VERIFY OTP
             * Verify OTP that was sent through chosen sign up method
             */
            verifyOtp()
            {
                let otpForm = this.$refs.otpForm;
                if(!this.config.btn_verify_otp.loading && otpForm.validate()) {
                    this.response.message = '';
                    this.response.errors  = {};
                    this.config.btn_verify_otp.loading = true;
                    this.request.identifier = this.response.identifier;
                    this.request.username        = this.request.username.toLowerCase();
                    this.request.email_or_mobile = this.request.email_or_mobile.toLowerCase();

                    api_auth.signUp.otpVerify(this.request).then(response => {
                        if(!response) return;

                        if(response.data.message != null) {
                            this.response.errors.otp = [response.data.message];
                            this.$store.commit('dialog/error/show', this.response);
                        }
                        else {
                            if(response.data.registered) {
                                this.config.step = 3;
                                this.$store.commit('dialog/message/show', {
                                    title  : 'Registered!',
                                    prompt : 'Thank you for signing up to ' + this.$store.getters['env/state'].app.name + '!',
                                    okIcon : 'start',
                                    okLabel: 'Get Started',
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
                        this.config.btn_verify_otp.loading = false;
                    }).catch(errors => {
                        this.config.btn_verify_otp.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                        otpForm.validate();
                    });
                }
            }
        }
    }
</script>

<style scoped>

</style>
