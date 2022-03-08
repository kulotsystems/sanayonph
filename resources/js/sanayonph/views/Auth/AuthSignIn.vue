<template>
    <v-app>
        <dialogs/>
        <v-main class="yellow">
            <bg-sign-in/>
            <div v-if="$store.state.auth.showIntro && !$route.query['signed-out'] && !$route.query['next']" style="height: 100%">
                <intro @skipIntro="$store.commit('auth/skipIntro')"/>
            </div>
            <template v-else>
                <v-container>
                    <!-- LOGO -->
                    <div class="mt-5" align="center">
                        <v-avatar size="128">
                            <v-img :src="config.logo" :alt="$store.getters['env/state'].app.name" aspect-ratio="1"/>
                        </v-avatar>
                    </div>

                    <v-row justify="center">
                        <v-col cols="12" sm="8" md="6" class="pa-5">
                            <v-card>
                                <v-card-text>
                                    <!-- INTRO -->
                                    <div class="mt-5 mb-8">
                                        <h2 class="primary--text font-weight-bold">Sign In</h2>
                                        <p class="primary--text">Explore {{ $store.getters['env/state'].app.name }} like it's your community.</p>
                                    </div>

                                    <!-- SIGN IN FORM -->
                                    <v-form ref="signInForm" @submit.prevent="signIn" class="mt-5">
                                        <v-row dense>
                                            <!-- EMAIL / MOBILE / USERNAME -->
                                            <v-col cols="12">
                                                <v-text-field
                                                    label="E-mail / Mobile No. / Username"
                                                    v-model="request.username"
                                                    :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response, 'username')]"
                                                    @keyup="$store.dispatch('form/reset', [response, 'username']).then(r => response)"
                                                    @focus="$store.dispatch('form/reset', [response, 'username']).then(r => response)"
                                                    outlined
                                                    prepend-inner-icon="email"
                                                    background-color="white"
                                                />
                                            </v-col>

                                            <!-- PASSWORD -->
                                            <v-col cols="12">
                                                <v-text-field
                                                    label="Password"
                                                    v-model="request.password"
                                                    :rules="[
                                                        $store.state.form.rules.required,
                                                        $store.state.form.rules.min_chars(request.password, $store.state.env.password.minChars),
                                                        $store.state.form.rules.feedback(response,'password')
                                                    ]"
                                                    @keyup="$store.dispatch('form/reset', [response, 'username']).then(r => response)"
                                                    @focus="$store.dispatch('form/reset', [response, 'username']).then(r => response)"
                                                    :append-icon="config.password.show ? 'visibility' : 'visibility_off'"
                                                    :type="config.password.show ? 'text' : 'password'"
                                                    @click:append="config.password.show = !config.password.show"
                                                    outlined
                                                    prepend-inner-icon="lock"
                                                    background-color="white"
                                                />

                                                <!-- PASSWORD RESET LINK -->
                                                <div class="" align="center">
                                                    <router-link :to="{ name: 'reset-password'}" class="primary--text text-decoration-none">Forgot password?</router-link>
                                                </div>
                                            </v-col>
                                        </v-row>

                                        <v-row dense>
                                            <!-- SIGN IN BUTTON -->
                                            <v-col cols="12" class="mt-4">
                                                <button-action
                                                    type="submit"
                                                    label="SIGN IN"
                                                    :icon="$store.getters['icon/state'].sign_in"
                                                    :loading="config.btn_sign_in.loading"
                                                    @click="signIn"
                                                    block
                                                    large
                                                />
                                            </v-col>
                                        </v-row>
                                    </v-form>

                                    <!-- SIGN UP LINK -->
                                    <div class="mt-5" align="center">
                                        Don't have an account? <router-link :to="{ name: 'sign-up'}" class="primary--text text-decoration-none">Sign Up</router-link>
                                    </div>

                                    <!-- SHOP LINK -->
                                    <div class="mt-3" align="center">
                                        Or visit our <router-link :to="{ name: 'shop'}" class="primary--text text-decoration-none">Shop</router-link> for now.
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </template>
        </v-main>
    </v-app>
</template>

<script>
    import api_auth from '../../api/api-auth.js';
    import logo     from '../../assets/img/logo.jpg';

    export default {
        name: 'AuthSignIn',
        components: {
            'bg-sign-in'   : () => import('../../components/backgrounds/BackgroundSignIn.vue'),
            'dialogs'      : () => import('../../components/dialogs/Dialogs.vue'),
            'button-action': () => import('../../components/buttons/ButtonAction.vue'),
            'intro'        : () => import('../../components/sheets/Intro.vue'),
        },
        data() {
            return {
                config: {
                    logo: logo,
                    password: {
                        show: false
                    },
                    btn_sign_in: {
                        loading: false,
                    }
                },
                request: {
                    username: '',
                    password: ''
                },
                response: {
                    message: '',
                    errors : {}
                }
            }
        },
        computed: {
            // computed toggleTry
            toggleTry() {
                return this.$store.getters['auth/toggleTry'];
            }
        },
        watch: {
            toggleTry() {
                if(this.$refs.signInForm != null)
                    this.signIn();
            }
        },
        methods : {

            /****************************************************************************************************
             * METHOD: SIGN IN
             * Send entered credentials for authentication
             */
            signIn()
            {
                let signInForm = this.$refs.signInForm;
                if(!this.config.btn_sign_in.loading && signInForm.validate()) {
                    this.response.message = '';
                    this.response.errors  = {};
                    this.config.btn_sign_in.loading = true;

                    api_auth.session().then(response => {
                        if(!response) return;

                        api_auth.signIn.proceed(this.request).then(response => {
                            if(!response) return;

                            if(response.data.signed_in) {
                                if(this.$route.query['next'])
                                    window.open(this.$router.history.base + decodeURI(this.$route.query['next']), '_self');
                                else {
                                    this.config.btn_sign_in.loading = false;
                                    this.$emit('signedIn');
                                }
                            }
                        }).catch(errors => {
                            this.config.btn_sign_in.loading = false;
                            this.response.message = errors.response.data.message;
                            this.response.errors  = errors.response.data.errors;
                            signInForm.validate();
                        });
                    });
                }
            }
        }
    }
</script>

<style scoped>

</style>
