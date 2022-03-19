<template>
    <div>
        <table>
            <td style="width: 35px; vertical-align: middle">
                <v-img
                    :src="user.avatar != null ? $store.getters['path/userAvatar']['064'] + '/' + user.avatar : $store.getters['path/defaultAvatar']"
                    aspect-ratio="1"
                    style="border-radius: 50%"
                />
            </td>
            <td class="primary--text pl-1" style="vertical-align: middle">
                <p class="ma-0" style="line-height: 1">{{ user.full_name }}</p>
                <p class="ma-0" style="line-height: 1"><small>@{{ user.username }}</small></p>
            </td>
        </table>
        <v-rating
            v-model="rating"
            background-color="primary lighten-3"
            color="primary"
            dense
            small
            readonly
        />
        <div class="primary--text">
            <small><b>{{ productLabel }}</b></small>
        </div>
        <p class="mb-1 text-body-2" style="white-space: pre-wrap;">{{ content }}</p>
        <p class="mb-0"><small>{{ dateTime }}</small></p>
    </div>



<!--    <v-list-item-group class="grey lighten-5 rounded-t rounded-b" :mandatory="!plain">-->
<!--        <v-list-item two-line :ripple="false" :disabled="plain">-->
<!--            <v-list-item-avatar>-->
<!--                <v-img-->
<!--                    :src="user.avatar != null ? $store.getters['path/userAvatar']['064'] + '/' + user.avatar : $store.getters['path/defaultAvatar']"-->
<!--                    aspect-ratio="1"-->
<!--                />-->
<!--            </v-list-item-avatar>-->
<!--            <v-list-item-content>-->
<!--                <v-list-item-title>-->
<!--                    <b class="primary&#45;&#45;text">{{ user.full_name }}</b>-->
<!--                    <br>-->
<!--                    <small class="primary&#45;&#45;text text-spaced">@{{ user.username }}</small>-->
<!--                </v-list-item-title>-->
<!--            </v-list-item-content>-->
<!--        </v-list-item>-->
<!--    </v-list-item-group>-->
</template>

<script>
    export default {
        name: 'ProductReview',
        components: {},
        props: {
            user: {
                type   : Object,
                default: {}
            },
            rating: {
                type   : Number,
                default: 0
            },
            content: {
                type   : String,
                default: ''
            },
            dateTime: {
                type   : String,
                default: ''
            },
            productLabel: {
                type   : String,
                default: ''
            }
        },
        data() {
            return {}
        },
        computed: {},
        methods : {}
    }
</script>

<style scoped>

</style>
