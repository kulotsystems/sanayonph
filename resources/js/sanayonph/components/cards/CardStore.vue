<template>
    <v-card v-if="store != null" hover elevation="8" exact router style="border-radius: 18px" :to="{name: 'profile-store-products', params: {username: store.user.username, store: store.store.slug}}">
        <v-img
            :src="store.user.avatar != null && store.user.avatar !== '' ? $store.getters['path/userAvatar']['256'] + '/' + store.user.avatar : $store.getters['path/defaultAvatar']"
            aspect-ratio="1"
        >
            <template v-slot:placeholder>
                <v-row class="fill-height ma-0" align="center" justify="center">
                    <v-progress-circular indeterminate color="primary lighten-3"/>
                </v-row>
            </template>
        </v-img>
        <v-card-title class="justify-center pb-0">
            <small class="primary--text one-line" :class="{'text-body-1': $vuetify.breakpoint.xs}">{{ store.user.name.full_name_1 }}</small>
        </v-card-title>
        <v-card-text class="pb-0 one-line" align="center">
            <small class="primary--text text-spaced">@{{ store.user.username }}</small>
        </v-card-text>
        <v-card-subtitle class="one-line" align="center">
            <small class="primary--text">{{ store.address }}</small>
        </v-card-subtitle>
    </v-card>
</template>

<script>
    export default {
        name: 'CardStore',
        components: {},
        props: {
            store: {
                type   : Object,
                default: null
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
    .one-line {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
