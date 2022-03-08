<template>
    <v-app>
        <template v-if="$store.getters['auth/loaded'] && Object.values(response.errors).length <= 0">
            <transition :name="config.transition">
                <router-view @signedIn="signIn" @signedOut="signOut"></router-view>
            </transition>
        </template>
        <app-default v-else :message="response.message" :errors="response.errors"/>
    </v-app>
</template>

<script>
    import api_auth from './api/api-auth.js';

    export default {
        name: 'Sanayon',
        components: {
            'app-default': () => import('./components/sheets/AppDefault.vue')
        },
        data() {
            return {
                config: {
                    transition: 'slide-left'
                },
                request: {

                },
                response: {
                    message: '',
                    errors : {}
                },
            }
        },
        computed: {
            isSignedOut() {
                return this.$store.getters['auth/signedOut'];
            },
            goBack() {
                return this.$store.getters['history/goBack'];
            }
        },
        watch: {
            // watch $route changes for document title and transition
            $route: {
                immediate: true,
                handler(to, from) {
                    let title = '';
                    if(to.meta.title) {
                        if(to.params.username)
                            title += '@' + to.params.username + ' ';
                        title += to.meta.title;
                        title += ' ~ ';
                    }
                    title += this.$store.getters['env/state'].app.name;
                    document.title = title;

                    try {
                        if(to.meta.order && from.meta.order)
                            this.config.transition = to.meta.order < from.meta.order ? 'slide-right' : 'slide-left';
                        else {
                            if(to.fullPath === '/')
                                this.config.transition = 'slide-right';
                            else if(from.fullPath === '/') {
                                this.config.transition = 'slide-left';
                                this.$store.commit('history/pushLeft', from);
                            }
                            else {
                                // remove extra trailing slash
                                const toPath   = to.path.endsWith('/') ? to.path.slice(0, -1) : to.path;
                                const fromPath = from.path.endsWith('/') ? from.path.slice(0, -1) : from.path;

                                // get splitted length
                                const toDepth   = toPath.split('/').length;
                                const fromDepth = fromPath.split('/').length;

                                // process slide
                                if(toDepth !== fromDepth) {
                                    if(toDepth < fromDepth) {
                                        this.config.transition = 'slide-right';
                                    }
                                    else {
                                        this.config.transition = 'slide-left';
                                        this.$store.commit('history/pushLeft', from);
                                    }
                                }
                                else
                                    this.config.transition = '';
                            }
                        }
                    } catch (e) {}
                }
            },

            // watch if user is signed out
            isSignedOut() {
                if(this.isSignedOut) {
                    this.$store.commit('dialog/message/show', {
                        prompt : 'You have been signed out',
                        title  : 'Unauthenticated',
                        okIcon : 'login',
                        okLabel: 'SIGN IN',
                        callback: {
                            async : true,
                            action: () => {
                                this.signOut();
                            }
                        },
                        persistent: true
                    });
                }
            },

            // watch when a route-back event is triggered
            goBack() {
                let navLefts = this.$store.getters['history/navLefts'];
                let navLeft  = this.$store.getters['history/navLeft'];
                let navRight = this.$store.getters['history/navRight'];

                if(navLeft != null) {
                    // if backing from a tabbed view
                    if(navRight.params.tab != null) {
                        if(navLefts[navLeft.name]) {
                            let index = this.$store.getters['tab/index'][navRight.name];
                            this.$router.go((index + 2) * -1);
                            this.$store.commit('history/popLeft', navLeft);
                        }
                        else
                            this.$router.replace(navLeft);
                    }

                    // else if backing from a single view
                    else {
                        if(Object.values(navLefts).length <= 0) {
                            if(navLeft.name)
                                this.$router.replace(navLeft);
                            else
                                this.$router.go(-1);
                        }
                        else
                            this.$router.go(-1);
                    }
                }
                else
                    window.location.reload();
            }
        },
        methods : {

            /****************************************************************************************************
             * METHOD: APPLY ROUTE RULES
             * Redirect based on the user's authentication status
             */
            applyRouteRules()
            {
                if(this.$route.meta.rules) {
                    let rules = this.$route.meta.rules;
                    if(this.$store.getters['auth/authenticated']) {
                        if(!rules.user.accept)
                            this.$router.replace(rules.user.redirect);
                    }
                    else {
                        if(!rules.guest.accept)
                            this.$router.replace(rules.guest.redirect);
                    }
                }
            },

            /****************************************************************************************************
             * METHOD: ACTIVATE ROUTE TAB
             * Set active tab if there is a :tab route parameter
             */
            activateRouteTab()
            {
                if(this.$route.params.tab == null) {
                    if(this.$route.meta.defaultTab != null) {
                        let routePath = this.$route.path.toString();
                        if(routePath.charAt(routePath.length - 1) === '/')
                            routePath = routePath.substr(0, routePath.length - 1);
                        this.$router.replace(routePath + '/' + this.$route.meta.defaultTab);
                    }
                }
            },

            /****************************************************************************************************
             * METHOD: SIGN IN
             * Handle user sign in (also called when 'signedIn' event was emitted)
             */
            signIn()
            {
                this.$store.commit('dialog/loader/show');
                api_auth.signIn.user().then(response => {
                    this.$store.commit('dialog/loader/hide');
                    if(!response) return;

                    this.$store.commit('auth/setUser', response.data.user);
                    this.$store.commit('auth/load');
                    this.applyRouteRules();
                    this.activateRouteTab();

                    // check username in router
                    if(response.data.user != null) {
                        if(this.$route.meta.rules.user.accept && !this.$route.meta.rules.guest.accept) {
                            if (this.$router.history.current.params.username) {
                                let enteredUsername = this.$router.history.current.params.username;
                                if (enteredUsername !== response.data.user.username) {
                                    const correct_url = this.$router.history.base + this.$router.history.current.fullPath.replace(enteredUsername, response.data.user.username);
                                    this.$store.commit('dialog/message/error403', correct_url);
                                }
                            }
                        }
                    }
                }).catch(errors => {
                    this.$store.commit('dialog/loader/hide');
                    this.response.message = errors.response.data.message;
                    this.response.errors  = errors.response.data.errors;
                    this.$store.commit('auth/load');
                    this.applyRouteRules();
                    this.activateRouteTab();
                });
            },

            /****************************************************************************************************
             * METHOD: SIGN OUT
             * Handle user sign out (also called when 'signedOut' event was emitted)
             */
            signOut()
            {
                this.$store.commit('store/setUser', this.$store.getters['auth/user']);
                this.$store.commit('auth/setUser', null);
                setTimeout(() => {
                    this.$store.commit('dialog/confirm/hide');
                    this.$store.commit('dialog/message/hide');
                }, 300);
                window.open(this.$router.options.base + '?signed-out=1', '_self', null, true);
            }
        },
        created() {
            // show dialog-loader while fetching a route view
            this.$router.beforeEach((to, from, next) => {
                this.$store.commit('dialog/loader/show');
                next();
            });

            // hide dialog-loader after fetching a route view
            this.$router.afterEach(() => {
                this.$store.commit('dialog/loader/hide');
            });

            // attempt to sign-in a user
            api_auth.session().then(response => {
                this.signIn();
            });
        }
    }
</script>

<style>
    body {
        overflow: hidden;
    }

    .blurred {
        opacity: 0.7;
    }

    .v-app-bar-title__content{
        text-overflow: clip;
        overflow: visible;
    }

    .v-toolbar .v-toolbar-text {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }

    .v-text-field.v-text-field--enclosed .v-text-field__details {
        margin-bottom: 0 !important;
    }

    .v-toolbar__extension .theme--light.v-tabs>.v-tabs-bar,
    .v-toolbar__extension .theme--dark.v-tabs>.v-tabs-bar {
        background: transparent;
    }

    .circle .cropper-view-box,
    .circle .cropper-face {
        border-radius: 50%;
    }

    .v-btn--loading {
        opacity: 0.7;
    }

    .v-btn--plain:not(.v-btn--active):not(.v-btn--loading):not(:focus):not(:hover) .v-btn__content {
        opacity: 1;
    }

    .v-input.uppercase input {
        text-transform: uppercase;
    }

    .v-input.centered input {
        text-align: center;
    }

    .v-input.right input {
        text-align: right;
    }

    .v-input.bold input {
        font-weight: bold;
    }

    .v-bottom-sheet > .v-card {
        border-radius: 12px 12px 0 0;
    }

    .v-bottom-navigation.block .v-btn .v-btn__content {
        flex-direction: unset;
    }

    .v-bottom-navigation.block .v-btn:not(.v-btn--active) {
        color: #b75c08 !important;
    }

    /****************************************************************************************************
     * ROUTE TRANSITIONS
     */
    .slide-left-enter-active,
    .slide-left-leave-active,
    .slide-right-enter-active,
    .slide-right-leave-active {
        transition-duration: 0.3s;
        transition-property: height, opacity, transform;
        transition-timing-function: cubic-bezier(0.55, 0, 0.1, 1);
        overflow: hidden;
    }
    .slide-left-enter,
    .slide-right-leave-active {
        opacity: 0;
        transform: translate(4em, 0);
    }
    .slide-left-leave-active,
    .slide-right-enter {
        opacity: 0;
        transform: translate(-2em, 0);
    }

    /****************************************************************************************************
     * LIST ANIMATIONS
     */
    .list-complete-item {
        transition: all 0.4s;
    }
    .list-complete-leave-active {
        position: absolute;
        display: none;
    }
</style>
