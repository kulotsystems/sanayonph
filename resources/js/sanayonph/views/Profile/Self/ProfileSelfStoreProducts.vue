<template>
    <v-app>
        <toolbar-main>
            <!-- ADD PRODUCT BUTTON -->
            <div slot="fab">
                <button-floating icon="add" exact router :to="{ name: 'profile-store-products-add', params: {tab: 'general'} }" right bottom/>
            </div>
        </toolbar-main>
        <v-main class="secondary">
            <bg-secondary/>
            <v-container class="pa-4 pa-sm-5">
                <!-- NO CATEGORIES -->
                <template v-if="config.categories.length <= 0">
                    <dialog-info v-if="config.loaded">
                        <p class="primary--text text-h6">Please add at least one product category first.</p>
                        <v-btn color="primary" exact router :to="{ name: 'profile-store-categories' }">Categories</v-btn>
                    </dialog-info>
                </template>

                <!-- HAS CATEGORIES: DISPLAY PRODUCTS -->
                <template v-else>
                    <div v-for="(category, i) in config.categories" :key="category.id">
                        <v-row>
                            <v-col class="pa-1 pa-sm-2 pb-sm-0" :class="{'mt-6': i > 0}">
                                <v-card color="primary" class="no-border-bottom-left-radius no-border-bottom-right-radius">
                                    <v-toolbar flat>
                                        <v-toolbar-title class="primary--text">
                                            <v-icon color="primary">{{ $store.getters['icon/state'].category }}</v-icon>
                                            <small class="text-button">{{ category.name }}</small>
                                        </v-toolbar-title>
                                    </v-toolbar>
                                </v-card>
                            </v-col>
                        </v-row>
                        <v-row v-if="category.products.length <= 0">
                            <v-col cols="12" class="white--text">
                                <v-icon style="opacity: 0.7" color="white">info</v-icon> No products in this category yet.
                            </v-col>
                        </v-row>
                        <transition-group v-else name="list-complete" tag="v-row">
                            <v-col class="pa-1 pt-1 pa-sm-2 pt-sm-2" cols="6" sm="4" md="3" v-for="(product, j) in category.products" :key="product.id" :class="{ 'list-complete-item': config.animation }">
                                <v-card hover :class="{ 'unpublished': !product.is_published, 'no-border-top-left-radius': j === 0, 'no-border-top-right-radius': ($vuetify.breakpoint.xs && j === 1) || ($vuetify.breakpoint.sm && j === 2) || ($vuetify.breakpoint.mdAndUp && j === 3)}">
                                    <v-carousel v-if="product.gen_images.length > 0 || product.var_images.length > 0" v-model="product.active_image" hide-delimiters show-arrows-on-hover :continuous="false" height="auto">
                                        <v-carousel-item v-for="(image, i) in product.gen_images.concat(product.var_images)" :key="i">
                                            <v-img
                                                :lazy-src="$store.getters['path/defaultProduct']"
                                                     :src="`${$store.getters['path/productImg'][imageDir]}/${image}`"
                                                aspect-ratio="1"
                                            >
                                                <template v-slot:placeholder>
                                                    <v-row class="fill-height ma-0" align="center" justify="center">
                                                        <v-progress-circular indeterminate color="primary lighten-3"/>
                                                    </v-row>
                                                </template>
                                            </v-img>
                                        </v-carousel-item>
                                    </v-carousel>
                                    <v-img v-else :src="$store.getters['path/defaultProduct']" aspect-ratio="1"/>
                                    <v-card-subtitle class="px-2 pt-2 pb-0">
                                        <div class="product-title secondary--text">
                                            <small><span style="opacity: 0.8;">{{ j+1 }}. </span>{{ product.name }}</small>
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
                                    <v-card-actions class="py-0">
                                        <v-switch
                                            inset
                                            flat
                                            v-model="product.is_published"
                                            @click="confirmPublish(i, j)"
                                            color="primary"
                                        />
                                        <v-spacer></v-spacer>
                                        <v-btn fab class="primary lighten-1" depressed x-small exact router :to="{ name: 'profile-store-products-edit', params: { product: product.id, tab: 'general'} }" @click="config.animation=false">
                                            <v-icon>edit</v-icon>
                                        </v-btn>
                                        <v-btn fab class="primary lighten-1" depressed x-small @click="confirmDelete(i, j)">
                                            <v-icon>delete</v-icon>
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-col>
                        </transition-group>
                    </div>
                </template>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    import api_product from '../../../api/api-product.js';

    export default {
        name: 'ProfileSelfStoreProducts',
        components: {
            'bg-secondary'   : () => import('../../../components/backgrounds/BackgroundSecondary.vue'),
            'dialog-info'    : () => import('../../../components/dialogs/DialogInfo.vue'),
            'toolbar-main'   : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'button-floating': () => import('../../../components/buttons/ButtonFloating.vue'),
        },
        data() {
            return {
                config: {
                    categories: [],
                    animation : false,
                    loaded    : false
                }
            }
        },
        computed: {
            // computed image directory
            imageDir() {
                return this.$vuetify.breakpoint.xs ? '128' : '300';
            }
        },
        methods : {
            /****************************************************************************************************
             * METHOD: CONFIRM DELETE
             * Confirm to delete selected product
             */
            confirmDelete(i, j) {
                let product = this.config.categories[i].products[j];
                this.$store.commit('dialog/confirm/show', {
                    title   : 'Delete Product',
                    prompt  : 'Do you really want to delete the following product?<div class="mt-5"><small class="text-body-1 primary--text">' + product.name + '</small></div>',
                    noIcon  : 'arrow_back',
                    noLabel : 'BACK',
                    yesIcon : 'delete',
                    yesLabel: 'DELETE',
                    yesClass: 'error',
                    yesCallback: {
                        async : true,
                        action: () => {
                            api_product.delete(product.id).then(response => {
                                if(!response) return;

                                if(response.data.deleted) {
                                    this.config.categories[i].products.splice(j, 1);
                                }
                                this.$store.commit('dialog/confirm/hide');
                            }).catch(errors => {
                                this.$store.commit('dialog/confirm/errors', errors);
                            });
                        }
                    }
                });
            },

            /****************************************************************************************************
             * METHOD: CONFIRM PUBLISH / UNPUBLISH
             * Confirm to publish or unpublish product
             */
            confirmPublish(i, j) {
                let product      = this.config.categories[i].products[j];
                let isPublishing = product.is_published;
                let term = isPublishing ? 'Publish' : 'Unpublish';
                this.$store.commit('dialog/confirm/show', {
                    title   : term + ' Product',
                    prompt  : 'Do you really want to ' + term.toLowerCase() + ' the following product?<div class="mt-5"><small class="text-body-1 primary--text">' + product.name + '</small></div>',
                    noIcon  : 'arrow_back',
                    noLabel : 'BACK',
                    yesIcon : isPublishing ? 'done' : 'clear',
                    yesLabel: term,
                    yesClass: isPublishing ? 'success' : 'error',
                    yesCallback: {
                        async : true,
                        action: () => {
                            api_product.publish({ is_publishing: isPublishing }, product.id).then(response => {
                                if(!response) return;

                                if(response.data.published) {
                                    this.config.categories[i].products[j].is_published = isPublishing;
                                    this.config.categories[i].products[j].published_at = response.data.published;
                                }
                                this.$store.commit('dialog/confirm/hide');
                            }).catch(errors => {
                                this.$store.commit('dialog/confirm/errors', errors);
                            });
                        }
                    },
                    noCallback: {
                        action: () => {
                            this.config.categories[i].products[j].is_published = !isPublishing;
                        }
                    },
                    persistent: true
                });
            }
        },
        created() {
            setTimeout(() => {
                this.$store.commit('dialog/loader/show');
                api_product.index().then(response => {
                    this.$store.commit('dialog/loader/hide');
                    if(!response) return;

                    this.config.categories = response.data.categories;
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
    .no-border-top-left-radius {
        border-top-left-radius: 0;
    }
    .no-border-top-right-radius {
        border-top-right-radius: 0;
    }
    .no-border-bottom-left-radius {
        border-bottom-left-radius: 0;
    }
    .no-border-bottom-right-radius {
        border-bottom-right-radius: 0;
    }
</style>
