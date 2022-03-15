<template>
    <v-app>
        <dialogs/>
        <toolbar-main @onBack="$emit('onBack', $event)"></toolbar-main>
        <v-main>
            <div class="with-bg" :style="{'background-image': $vuetify.breakpoint.xs ? `url(${$store.getters['path/yellowBGMobile']})` : `url(${$store.getters['path/yellowBGDesktop']})`}"/>
            <v-container>

                <!-- Search form -->
                <v-form @submit.prevent="search" class="mt-5" >

                    <transition>
                        <div v-show="lastQuery == null" class="align-center" >
                            <h1 class="primary--text" >Search products near you...</h1>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad.</p>
                        </div>
                    </transition>

                    <v-text-field
                        label="Enter your query here..."
                        v-model="query"
                        outlined
                        prepend-inner-icon="search"
                        background-color="white"
                    />

                    <!-- Maybe some "trending" clickable chips here for autosearch
                    <div>
                        <v-chip color="orange" >Clothing</v-chip>
                        <v-chip color="orange" >Food</v-chip>
                        <v-chip color="orange" >Clothing budol</v-chip>
                    </div>
                     -->
                </v-form>

                <!-- HAS RETURNED PRODUCTS -->
                <div v-show="products.length" class="my-3" >
                    <div class="d-flex primary--text mb-2">
                        <v-icon color="primary" >shopping_bag</v-icon>
                        <h3 class="ml-2" >Products</h3>
                    </div>
                    <transition-group name="list-complete" tag="v-row">
                        <v-col class="pa-1 pt-1 pa-sm-2 pt-sm-2" cols="6" sm="4" md="3" v-for="(product, j) in products" :key="product.id" :class="{ 'list-complete-item': config.animation }">
                            <v-card hover exact router :to="{ name: 'profile-store-products-show', params: { product: product.id }}" :class="{'no-border-top-left-radius': j === 0, 'no-border-top-right-radius': ($vuetify.breakpoint.xs && j === 1) || ($vuetify.breakpoint.sm && j === 2) || ($vuetify.breakpoint.mdAndUp && j === 3)}">
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
                </div>

                <!-- HAS RETURNED STORES -->
                <div v-show="stores.length" class="my-3" >
                    <div class="d-flex primary--text mb-5">
                        <v-icon color="primary" >storefront</v-icon>
                        <h3 class="ml-2" >Stores ({{  stores.length }})</h3>
                    </div>
                    <transition-group name="list-complete" tag="v-row">
                        <v-col class="pa-1 pt-1 pa-sm-2 pt-sm-2" cols="6" sm="4" md="3" v-for="(store, index) in stores" :key="store.store.id" :class="{ 'list-complete-item': config.animation }">
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

            <div v-show="isValidSearch == false" id="tempobg" ></div>
        </v-main>
        <nav-bottom></nav-bottom>
    </v-app>
</template>

<script>
    import api_explore   from '../../api/api-explore.js';

    export default {
        name: 'Explore',
        components: {
            'dialogs'     : () => import('../../components/dialogs/Dialogs.vue'),
            'toolbar-main': () => import('../../components/toolbars/ToolbarMain.vue'),
            'nav-bottom'  : () => import('../../components/navs/NavBottom.vue')
        },
        data() {
            return {
                query: '',
                lastQuery: null,
                products: [],
                stores: [],
                config: {
                    animation : false,
                    loaded    : false
                }
            }
        },
        computed: {
            isValidSearch(){
                return ( this.stores.length > 0 || this.products.length > 0 );
            }
        },
        mounted(){
            // Execute search onload when the "q" exist
            if( this.$route.query.q != undefined ){
                this.query = decodeURI(this.$route.query.q);
                this.search();
            }
        },
        methods: {
            search(){
                api_explore.query(this.query).then(response => {

                    // Replace the query
                    if( this.lastQuery != this.query ){
                        this.$router.replace({
                            path: this.$route.path,
                            query: {
                                q: encodeURI(this.query)
                            }
                        });
                    }

                    // Place the sweets
                    this.lastQuery = this.query;
                    this.stores = response.data.stores;
                    this.products = response.data.products;
                }).catch(errors => {
                    this.$store.commit('dialog/loader/hide');
                    this.$store.commit('dialog/error/show', errors);
                });
            }
        }
    }
</script>

<style scoped>
    #tempobg{
        min-height: 75%;
        background: url("https://www.sanayonph.com/images/sign-in-bg-desktop-v2.png");
        opacity: 0.2;
    }
</style>
