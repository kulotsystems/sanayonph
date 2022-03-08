<template>
    <v-app>
        <toolbar-main
            :custom-title="`@${$store.getters['store/user'].username} Products`"
        />

        <v-main class="yellow">
            <bg-yellow/>
            <v-container class="pa-2 pa-sm-3" v-if="catalog != null">
                <!-- NO CATALOG -->
                <dialog-info v-if="catalog.length <= 0">
                    <span class="primary--text text-h6"><b>@{{ $store.getters['store/user'].username }}</b> has no products to show yet.</span>
                </dialog-info>

                <!-- HAS CATALOG -->
                <template v-else>
                    <v-card v-for="(category, i) in catalog" :key="i" :class="{'mb-4': i < (catalog.length - 1) && !$vuetify.breakpoint.xs, 'mb-2': i < (catalog.length - 1) && $vuetify.breakpoint.xs}">
                        <v-toolbar class="grey lighten-5" dense flat>
                            <v-toolbar-title>
                                <v-icon class="grey--text text--lighten-1">{{ $store.getters['icon/state'].category }}</v-icon>
                                <small class="grey--text">{{ category.name }}</small>
                            </v-toolbar-title>
                        </v-toolbar>
                        <v-card-text class="grey lighten-5 pa-3 pa-sm-4">
                            <v-row v-if="category.published_products.length <= 0">
                                <v-col cols="12">
                                    <v-icon style="opacity: 0.7">info</v-icon> No products in this category yet.
                                </v-col>
                            </v-row>
                            <transition-group v-else name="list-complete" tag="v-row">
                                <v-col class="pa-1 pa-sm-2" cols="6" sm="4" md="3" v-for="(product, j) in category.published_products" :key="product.id" :class="{ 'list-complete-item': config.animation }">
                                    <v-card hover exact router :to="{ name: 'profile-store-products-show', params: { product: product.id }}">
                                        <v-img v-if="product.gen_images.length > 0"
                                               :lazy-src="$store.getters['path/defaultProduct']"
                                                    :src="`${$store.getters['path/productImg'][imageDir]}/${product.gen_images[0]}`"
                                               aspect-ratio="1"
                                        >
                                            <template v-slot:placeholder>
                                                <v-row class="fill-height ma-0" align="center" justify="center">
                                                    <v-progress-circular indeterminate color="primary lighten-3"/>
                                                </v-row>
                                            </template>
                                        </v-img>
                                        <v-img v-else-if="product.var_images.length > 0"
                                               :lazy-src="$store.getters['path/defaultProduct']"
                                                    :src="`${$store.getters['path/productImg'][imageDir]}/${product.var_images[0]}`"
                                               aspect-ratio="1"
                                        >
                                            <template v-slot:placeholder>
                                                <v-row class="fill-height ma-0" align="center" justify="center">
                                                    <v-progress-circular indeterminate color="primary lighten-3"/>
                                                </v-row>
                                            </template>
                                        </v-img>
                                        <v-img v-else
                                               :src="$store.getters['path/defaultProduct']"
                                               aspect-ratio="1"
                                        />

                                        <v-card-subtitle class="px-2 pt-2 pb-0">
                                            <div class="product-title secondary--text">
                                                <small>
                                                    <!--<span style="opacity: 0.8;">{{ j+1 }}. </span>-->
                                                    {{ product.name }}
                                                </small>
                                            </div>
                                            <div class="product-subtitle mt-2">
                                                <small>{{ category.name }}</small>
                                            </div>
                                        </v-card-subtitle>
                                        <v-card-text class="pa-0 px-1 pr-3 grey lighten-3" align="right">
                                            <table style="width: 100%">
                                                <tr>
                                                    <td class="primary--text" style="vertical-align: top">
                                                        <div style="display: inline-block" align="center">
                                                            <small>
                                                                <b>{{ Number(product.pricing.stock.total).toLocaleString() }}</b>
                                                                Stock
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td align="right">
                                                        &#8369;
                                                        <big>
                                                            <b>{{ Number(product.pricing.price.min).toLocaleString() }}</b>
                                                            <br>
                                                            <template v-if="product.pricing.price.min !== product.pricing.price.max">
                                                                ~ <b>{{ Number(product.pricing.price.max).toLocaleString() }}</b>
                                                            </template>
                                                            <template v-else>
                                                                &nbsp;
                                                            </template>
                                                        </big>
                                                    </td>
                                                </tr>
                                            </table>
                                        </v-card-text>
                                    </v-card>

                                </v-col>
                            </transition-group>
                        </v-card-text>
                    </v-card>
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
            'toolbar-main': () => import('../../../components/toolbars/ToolbarMain.vue')
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
            // computed image directory
            imageDir() {
                return this.$vuetify.breakpoint.xs ? '128' : '300';
            },

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
    .product-title {
        height: 2.6rem;
        font-size: 1.3rem;
        line-height: 1.3rem;
        font-weight: 500;
        max-height: 4rem;
        overflow: hidden;
        display: block;
        -webkit-line-clamp: 2;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        text-overflow: ellipsis;
        white-space: normal;
    }
    .product-subtitle {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .unpublished {
        opacity: 0.7;
    }
</style>