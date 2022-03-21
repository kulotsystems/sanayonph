<template>
    <v-app>
        <dialogs/>
        <toolbar-main/>
        <v-main class="yellow">
            <bg-yellow/>
            <v-container class="pa-4 pa-sm-5">
                <!-- Search form -->
                <v-row>
                    <v-col class="pa-1 pa-sm-2 pb-sm-0" cols="12">
                        <v-form @submit.prevent="search(false)">
                            <v-text-field
                                label="Search"
                                v-model="request.query"
                                outlined
                                prepend-inner-icon="search"
                                background-color="white"
                            />
                        </v-form>
                    </v-col>
                </v-row>

                <!-- HAS SEARCHED PRODUCTS -->
                <div v-if="config.products.length > 0">
                    <v-row>
                        <v-col class="pa-1 pa-sm-2 pb-sm-0">
                            <v-card color="primary">
                                <v-toolbar flat dense>
                                    <v-toolbar-title class="primary--text">
                                        <v-icon color="primary">{{ $store.getters['icon/state'].product }}</v-icon>
                                        <span class="text-button">Product<template v-if="config.products.length > 1">s</template> ({{  config.products.length }})</span>
                                    </v-toolbar-title>
                                </v-toolbar>
                            </v-card>
                        </v-col>
                    </v-row>
                    <transition-group name="list-complete" tag="v-row">
                        <v-col class="pa-1 pt-1 pa-sm-2 pt-sm-2" cols="6" sm="4" md="3" v-for="(product, j) in config.products" :key="product.id" :class="{ 'list-complete-item': config.animation }">
                            <card-product
                                :product="product"
                                :category="product.category"
                                :store="product.category.store"
                                :user="product.user"
                            />
                        </v-col>
                    </transition-group>
                </div>

                <!-- HAS SEARCHED STORES -->
                <div v-if="config.stores.length > 0" :class="{'mt-6': config.products.length > 0}">
                    <v-row>
                        <v-col class="pa-1 pa-sm-2 pb-sm-0">
                            <v-card color="primary">
                                <v-toolbar flat dense>
                                    <v-toolbar-title class="primary--text">
                                        <v-icon color="primary">storefront</v-icon>
                                        <span class="text-button">Store<template v-if="config.stores.length > 1">s</template> ({{  config.stores.length }})</span>
                                    </v-toolbar-title>
                                </v-toolbar>
                            </v-card>
                        </v-col>
                    </v-row>
                    <transition-group name="list-complete" tag="v-row">
                        <v-col class="pa-1 pt-1 pa-sm-2 pt-sm-2" cols="6" sm="4" md="3" v-for="(store, index) in config.stores" :key="store.store.id" :class="{ 'list-complete-item': config.animation }">
                            <card-store
                                :store="store"
                            />
                        </v-col>
                    </transition-group>
                </div>
            </v-container>
        </v-main>
        <nav-bottom/>
    </v-app>
</template>

<script>
    import api_explore   from '../../api/api-explore.js';

    export default {
        name: 'Explore',
        components: {
            'bg-yellow'   : () => import('../../components/backgrounds/BackgroundYellow.vue'),
            'dialogs'     : () => import('../../components/dialogs/Dialogs.vue'),
            'toolbar-main': () => import('../../components/toolbars/ToolbarMain.vue'),
            'nav-bottom'  : () => import('../../components/navs/NavBottom.vue'),
            'card-product': () => import('../../components/cards/CardProduct.vue'),
            'card-store'  : () => import('../../components/cards/CardStore.vue')
        },
        data() {
            return {
                config: {
                    products  : [],
                    stores    : [],
                    animation : true,
                    loaded    : false
                },
                request: {
                    query    : '',
                    lastQuery: '',
                },
                response: {
                    message: '',
                    errors : {}
                }
            }
        },
        computed: {
            isValidSearch(){
                return ( this.config.stores.length > 0 || this.config.products.length > 0 );
            },
        },
        methods: {
            /****************************************************************************************************
             * METHOD: SEARCH
             * Execute search
             */
            search(onload) {
                this.$store.commit('dialog/loader/show');
                api_explore.query(this.request.query).then(response => {
                    this.$store.commit('dialog/loader/hide');
                    if(!response) return;

                    // replace the query in route
                    if(!onload && this.request.query !== this.request.lastQuery) {
                        this.$router.replace({
                            path: this.$route.path,
                            query: {
                                q: encodeURI(this.request.query)
                            }
                        });
                    }

                    this.request.lastQuery = this.request.query;
                    this.config.stores     = response.data.stores;
                    this.config.products   = response.data.products;
                }).catch(errors => {
                    this.$store.commit('dialog/loader/hide');
                    this.$store.commit('dialog/error/show', errors);
                });
            }
        },
        mounted() {
            // Execute search onload when the "q" exist
            if(this.$route.query.q != null) {
                this.request.query = decodeURI(this.$route.query.q);
                this.search(true);
            }
        },
    }
</script>

<style scoped>

</style>
