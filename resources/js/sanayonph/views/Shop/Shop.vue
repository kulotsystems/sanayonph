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
                        <card-store
                            :store="store"
                        />
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
            'nav-bottom'  : () => import('../../components/navs/NavBottom.vue'),
            'card-store'  : () => import('../../components/cards/CardStore.vue')
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

</style>
