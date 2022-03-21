<template>
    <v-app>
        <toolbar-main
            :custom-title="`@${$store.getters['store/user'].username}` + ($vuetify.breakpoint.smAndUp ? ' Products' : '')"
        />

        <v-main class="yellow">
            <bg-yellow/>
            <v-container class="pa-4 pa-sm-5" v-if="catalog != null">
                <!-- NO CATALOG -->
                <dialog-info v-if="catalog.length <= 0">
                    <span class="primary--text text-h6"><b>@{{ $store.getters['store/user'].username }}</b> has no products to show yet.</span>
                </dialog-info>

                <!-- HAS CATALOG -->
                <template v-else>
                    <div v-for="(category, i) in catalog" :key="category.id">
                       <v-row>
                           <v-col class="pa-1 pa-sm-2 pb-sm-0" :class="{'mt-6': i > 0}">
                               <v-card color="primary">
                                   <v-toolbar flat dense>
                                       <v-toolbar-title class="primary--text">
                                           <v-icon color="primary">{{ $store.getters['icon/state'].category }}</v-icon>
                                           <span class="text-button">{{ category.name }}</span>
                                       </v-toolbar-title>
                                   </v-toolbar>
                               </v-card>
                           </v-col>
                       </v-row>
                        <transition-group name="list-complete" tag="v-row">
                            <v-col class="pa-1 pt-1 pa-sm-2 pt-sm-2" cols="6" sm="4" md="3" v-for="(product, j) in category.published_products" :key="product.id" :class="{ 'list-complete-item': config.animation }">
                                <card-product
                                    :product="product"
                                    :category="category"
                                    :store="$store.getters['store/user'].store"
                                    :user="$store.getters['store/user']"
                                    in-store
                                />
                            </v-col>
                        </transition-group>
                    </div>
                </template>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    import api_store from '../../../api/api-store.js';

    export default {
        name: 'ProfileGuestStoreProducts',
        components: {
            'bg-yellow'   : () => import('../../../components/backgrounds/BackgroundYellow.vue'),
            'dialog-info' : () => import('../../../components/dialogs/DialogInfo.vue'),
            'toolbar-main': () => import('../../../components/toolbars/ToolbarMain.vue'),
            'card-product': () => import('../../../components/cards/CardProduct.vue')
        },
        data() {
            return {
                config: {
                    animation : false,
                    loaded    : false
                },
                request: {

                },
                response: {
                    message: '',
                    errors : {}
                },
                catalogCacheCtr: 0
            }
        },
        computed: {
            // computed catalog
            catalog() {
                let ctr = this.catalogCacheCtr;
                return this.$store.getters['store/catalogs'][`${this.$route.params.username}_${this.$route.params.store}`];
            }
        },
        methods : {},
        mounted() {
            if(this.catalog == null) {
                setTimeout(() => {
                    this.$store.commit('dialog/loader/show');
                    api_store.products().then(response => {
                        this.$store.commit('dialog/loader/hide');
                        if(!response) return;

                        // commit catalog to vuex store module
                        this.$store.commit('store/addCatalog', {
                            username  : this.$route.params.username,
                            store     : this.$route.params.store,
                            categories: response.data.categories
                        });
                        this.catalogCacheCtr += 1;
                        this.config.loaded = true;

                        // enable animation after a delay
                        setTimeout(() => {
                            this.config.animation = true;
                        }, 500);
                    }).catch(errors => {
                        this.$store.commit('dialog/loader/hide');
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
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
