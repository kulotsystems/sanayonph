<template>
    <v-list-item-group class="grey lighten-5 rounded-t rounded-b" :mandatory="!plain">
        <v-list-item two-line :ripple="false" :disabled="plain">
            <v-list-item-avatar>
                <v-img
                    :src="avatar != null ? $store.getters['path/userAvatar']['064'] + '/' + avatar : $store.getters['path/defaultAvatar']"
                    aspect-ratio="1"
                />
            </v-list-item-avatar>
            <v-list-item-content>
                <v-list-item-title>
                    <b class="primary--text">{{ fullName }}</b>
                    <br>
                    <small class="primary--text">@{{ username }}</small>
                </v-list-item-title>
            </v-list-item-content>
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
                default: 'buyer'
            },
            fullName: {
                type   : String,
                default: 'Buyer Fullname'
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

        }
    }
</script>

<style scoped>

</style>
