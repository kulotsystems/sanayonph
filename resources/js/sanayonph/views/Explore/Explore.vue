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
                        <v-form @submit.prevent="search">
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

                <!-- HAS RETURNED PRODUCTS -->
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
                            <v-card hover exact router style="border-radius: 18px" :to="{ name: 'profile-store-products-show', params: { username: product.username, store: product.category.store.slug, product: product.id }}">
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
                                        <small class="primary--text">@{{ product.username }}</small>
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
                </div>

                <!-- HAS RETURNED STORES -->
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
            'nav-bottom'  : () => import('../../components/navs/NavBottom.vue')
        },
        data() {
            return {
                config: {
                    products  : [],
                    stores    : [],
                    animation : false,
                    loaded    : false
                },
                request: {
                    query    : '',
                    lastQuery: null,
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

            // computed image directory
            imageDir() {
                return this.$vuetify.breakpoint.xs ? '128' : '300';
            },
        },
        methods: {
            search(){
                this.$store.commit('dialog/loader/show');
                api_explore.query(this.request.query).then(response => {
                    this.$store.commit('dialog/loader/hide');
                    if(!response) return;

                    // Replace the query
                    if( this.request.lastQuery != this.request.query ){
                        this.$router.replace({
                            path: this.$route.path,
                            query: {
                                q: encodeURI(this.request.query)
                            }
                        });
                    }

                    // Place the sweets
                    this.request.lastQuery = this.request.query;
                    this.config.stores     = response.data.stores;
                    this.config.products   = response.data.products;
                }).catch(errors => {
                    this.$store.commit('dialog/loader/hide');
                    this.$store.commit('dialog/error/show', errors);
                });
            }
        },
        mounted(){
            // Execute search onload when the "q" exist
            if( this.$route.query.q != undefined ){
                this.request.query = decodeURI(this.$route.query.q);
                this.search();
            }
        },
    }
</script>

<style scoped>
    .one-line {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
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
</style>
