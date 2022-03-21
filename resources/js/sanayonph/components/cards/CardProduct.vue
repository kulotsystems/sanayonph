<template>
    <v-card v-if="product != null" hover exact router style="border-radius: 18px" :to="{ name: 'profile-store-products-show', params: { username: user.username, store: store.slug, product: product.id }}">
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
                <small v-if="inStore">{{ category.name }}</small>
                <small v-else class="primary--text text-spaced">@{{ user.username }}</small>
            </div>
        </v-card-subtitle>
        <v-card-text class="pa-0 px-1 pr-3 grey lighten-3" align="right" style="border-bottom-left-radius: 18px; border-bottom-right-radius: 18px">
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
</template>

<script>
    export default {
        name: 'CardProduct',
        components: {},
        props: {
            product: {
                type   : Object,
                default: null
            },
            category: {
                type   : Object,
                default: null
            },
            store: {
                type   : Object,
                default: null
            },
            user: {
                type   : Object,
                default: null
            },
            inStore: {
                type   : Boolean,
                default: false
            }
        },
        data() {
            return {}
        },
        computed: {
            // computed image directory
            imageDir() {
                return this.$vuetify.breakpoint.xs ? '128' : '300';
            },
        },
        methods : {}
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
</style>
