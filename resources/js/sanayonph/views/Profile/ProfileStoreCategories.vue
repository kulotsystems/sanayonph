<template>
    <v-app>
        <dialogs/>
        <profile-self-store-categories  v-if="isUsernameSelf"/>
        <profile-guest-store-categories v-else-if="this.config.loaded"/>
        <app-default v-else :message="response.message" :errors="response.errors"/>
    </v-app>
</template>

<script>
    import ProfileGuestStoreCategories from './Guest/ProfileGuestStoreCategories.vue';
    import ProfileSelfStoreCategories  from './Self/ProfileSelfStoreCategories.vue';
    import api_store from '../../api/api-store.js';

    export default {
        name: 'ProfileStoreCategories',
        components: {
            'profile-guest-store-categories': ProfileGuestStoreCategories,
            'profile-self-store-categories' : ProfileSelfStoreCategories,
            'dialogs'    : () => import('../../components/dialogs/Dialogs.vue'),
            'app-default': () => import('../../components/sheets/AppDefault.vue')
        },
        data() {
            return {
                config: {
                    loaded: false
                },
                request: {

                },
                response: {
                    message: '',
                    errors : {}
                }
            }
        },
        computed: {
            isUsernameSelf() {
                let bool = false;
                if(this.$store.getters['auth/user'] != null) {
                    if(this.$router.history.current.params.username) {
                        if(this.$store.getters['auth/user'].username === this.$router.history.current.params.username)
                            bool = true;
                    }
                }
                return bool;
            }
        },
        methods: {},
        beforeMount() {
            if(this.isUsernameSelf)
                this.config.loaded = true;
            else if(this.$store.getters['store/user'] != null) {
                if(this.$store.getters['store/user'].username === this.$route.params.username)
                    this.config.loaded = true;
            }
            if(!this.config.loaded) {
                api_store.user(this.$route.params.username).then(response => {
                    if(!response) return;

                    this.config.loaded = true;
                    this.$store.commit('store/setUser', response.data.user);
                }).catch(errors => {
                    this.response.message = errors.response.data.message;
                    this.response.errors  = errors.response.data.errors;
                });
            }
        }
    }
</script>

<style scoped>

</style>
