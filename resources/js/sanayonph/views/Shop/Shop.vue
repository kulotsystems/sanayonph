<template>
    <v-app>
        <dialogs/>
        <toolbar-main/>
        <v-main class="yellow">
            <bg-yellow/>
            <v-container class="pa-4 pa-sm-5">
                <!-- NO STORES -->
                <template v-if="$store.getters['store/stores'].length <= 0">
                    <v-alert v-if="config.loaded" prominent outlined color="primary" class="mb-0">
                        <v-row align="center">
                            <v-col class="grow">
                                No stores have published their products at the moment.
                            </v-col>
                        </v-row>
                    </v-alert>
                </template>

                <!-- HAS STORES -->
                <transition-group v-else name="list-complete" tag="v-row">
                    <v-col class="pa-1 pt-1 pa-sm-2 pt-sm-2" cols="6" sm="4" md="3" v-for="(store, index) in $store.getters['store/stores']" :key="store.store.id" :class="{ 'list-complete-item': config.animation }">
                        <v-card hover elevation="8" exact router style="border-radius: 18px" :to="{name: 'profile-store-products', params: {username: store.user.username, store: store.store.slug}}">
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
                    </v-col>
                </transition-group>
            </v-container>
        </v-main>
        <nav-bottom/>
    </v-app>
</template>

<script>
    import api_shop   from '../../api/api-shop.js';

    export default {
        name: 'Shop',
        components: {
            'bg-yellow'   : () => import('../../components/backgrounds/BackgroundYellow.vue'),
            'dialogs'     : () => import('../../components/dialogs/Dialogs.vue'),
            'toolbar-main': () => import('../../components/toolbars/ToolbarMain.vue'),
            'nav-bottom'  : () => import('../../components/navs/NavBottom.vue')
        },
        data() {
            return {
                config: {
                    animation : false,
                    loaded    : false
                }
            }
        },
        computed: {},
        methods : {},
        created() {
            if(this.$store.getters['store/stores'].length <= 0) {
                setTimeout(() => {
                    this.$store.commit('dialog/loader/show');
                    api_shop.stores().then(response => {
                        this.$store.commit('dialog/loader/hide');
                        if(!response) return;

                        this.$store.commit('store/setStores',  response.data.stores);
                        this.config.loaded = true;

                        // enable animation after a delay
                        setTimeout(() => {
                            this.config.animation = true;
                        }, 500);
                    }).catch(errors => {
                        this.$store.commit('dialog/loader/hide');
                        this.$store.commit('dialog/error/show', errors);
                    });
                }, 350);
            }
            else {
                // enable animation after a delay
                setTimeout(() => {
                    this.config.animation = true;
                }, 500);
            }
        }
    }
</script>

<style scoped>
    .one-line {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
