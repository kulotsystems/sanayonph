<template>
    <v-list-item-group class="grey lighten-5 rounded-t rounded-b" :mandatory="!plain">
        <v-list-item two-line :ripple="plain" :disabled="plain">
            <v-list-item-avatar>
                <v-img
                    :src="avatar != null && avatar !== '' ? $store.getters['path/userAvatar']['064'] + '/' + avatar : $store.getters['path/defaultAvatar']"
                    aspect-ratio="1"
                />
            </v-list-item-avatar>
            <v-list-item-content>
                <v-list-item-title>
                    <b class="primary--text">{{ fullName }}</b>
                    <br>
                    <small class="primary--text text-spaced">@{{ username }}</small>
                </v-list-item-title>
            </v-list-item-content>
            <v-list-item-action v-if="!plain">
                <v-btn color="primary" small text outlined @click="goBack">Products</v-btn>
            </v-list-item-action>
        </v-list-item>
    </v-list-item-group>
</template>

<script>
    export default {
        name: 'StoreLink',
        components: {},
        props: {
            avatar: {
                type   : String,
                default: ''
            },
            username: {
                type   : String,
                default: 'seller'
            },
            fullName: {
                type   : String,
                default: 'Seller Fullname'
            },
            store: {
                type  : String,
                default: 'store'
            },
            navBack: {
                type   : Boolean,
                default: false
            },
            plain: {
                type   : Boolean,
                default: false
            }
        },
        data() {
            return {

            }
        },
        computed: {},
        methods : {
            /****************************************************************************************************
             * METHOD: GO BACK
             * Navigate back to 'profile-store-products' view
             */
            goBack() {
                if(this.navBack) {
                    this.$store.commit('history/goBack', {
                        navRight: {name: this.$route.name, params: this.$route?.params},
                        navLeft : {name: 'profile-store-products'}
                    });
                }
                else {
                    this.$router.push({
                        name  : 'profile-store-products',
                        params: {
                            username: this.username,
                            store   : this.store
                        }
                    });
                }
            },
        }
    }
</script>

<style scoped>

</style>
