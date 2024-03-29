<template>
    <v-app>
        <dialogs/>
        <toolbar-main></toolbar-main>
        <v-main class="yellow">
            <bg-yellow/>
            <v-container class="pa-2 pa-sm-3" v-if="orders != null">
                <!-- NO ORDERS -->
                <template v-if="orders.length <= 0">
                    <dialog-info v-if="config.loaded">
                        <span class="primary--text text-h6">You have no orders yet.</span>
                    </dialog-info>
                </template>

                <!-- HAS ORDERS -->
                <transition-group v-else name="list-complete" tag="v-row">
                    <v-col cols="12" sm="6" v-for="(order, index) in orders" :key="order.id" :class="{'pb-2': index < (orders.length - 1) && !$vuetify.breakpoint.xs, 'pb-0': index < (orders.length - 1) && $vuetify.breakpoint.xs, 'list-complete-item': config.animation}">
                        <v-card hover exact router :to="{ name: 'profile-orders-details', params: { order: order.id } }">
                            <v-toolbar class="grey lighten-5" dense flat>
                                <v-icon class="grey--text text--lighten-1 mr-1" small>{{ $store.getters['icon/state'].order }}</v-icon>
                                <span class="v-toolbar-text text-body font-weight-light grey--text">{{ index + 1 }}. {{ order.ordered_at_str }}</span>
                            </v-toolbar>
                            <v-card>
                                <v-card-text>
                                    <table style="width: 100%">
                                        <tr>
                                            <td>ORDER ID</td>
                                            <td class="px-2">:</td>
                                            <td class="font-weight-bold primary--text">
                                                {{ order.id.toString().padStart(5, '0') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>STATUS</td>
                                            <td class="px-2">:</td>
                                            <td class="font-weight-bold primary--text">
                                                <p class="ma-0">{{ order.order_status }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <v-divider class="mt-2"></v-divider>
                                                <v-list-item two-line :ripple="false">
                                                    <v-list-item-avatar rounded="0" size="72">
                                                        <v-img
                                                            :lazy-src="$store.getters['path/default']"
                                                            :src="order.sale.product_image != null ? $store.getters['path/productImg']['064'] + '/' + order.sale.product_image : $store.getters['path/defaultProduct']"
                                                            aspect-ratio="1"
                                                            style="border-radius: 4px;"
                                                        />
                                                    </v-list-item-avatar>
                                                    <v-list-item-content>
                                                        <b class="primary--text">
                                                            {{ order.sale.product_name }}
                                                        </b>
                                                        <br>
                                                        <small class="primary--text">{{ order.sale.product_label }} (x{{ order.sale.quantity }})</small>
                                                        <br>
                                                        <small class="primary--text">
                                                            {{ order.seller.name.full_name_1 }}<br>@{{ order.seller.username }}
                                                        </small>
                                                    </v-list-item-content>
                                                    <v-list-item-action>
                                                        <v-btn text>
                                                            <span class="font-weight-thin">&#8369;</span> {{ Number(order.sale.price_after_quantity).toLocaleString() }}
                                                        </v-btn>
                                                    </v-list-item-action>
                                                </v-list-item>
                                                <v-divider class="mb-2"></v-divider>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>DELIVERY METHOD</td>
                                            <td class="px-2">:</td>
                                            <td class="primary--text">
                                                <p class="ma-0">{{ order.delivery_method_name }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>PAYMENT METHOD</td>
                                            <td class="px-2">:</td>
                                            <td class="primary--text">
                                                <p class="ma-0">{{ order.payment_method_name }}</p>
                                            </td>
                                        </tr>
                                    </table>
                                </v-card-text>
                            </v-card>
                        </v-card>
                    </v-col>
                </transition-group>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    import api_order from '../../../api/api-order.js';

    export default {
        name: 'ProfileSelfOrdersOLD',
        components: {
            'bg-yellow'   : () => import('../../../components/backgrounds/BackgroundYellow.vue'),
            'dialogs'     : () => import('../../../components/dialogs/Dialogs.vue'),
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
                ordersCacheCtr: 0
            }
        },
        computed: {
            // computed self orders
            orders() {
                let ctr = this.ordersCacheCtr;
                return this.$store.getters['auth/data/selfOrders'];
            }
        },
        methods : {},
        created() {
            if(this.orders == null) {
                setTimeout(() => {
                    this.$store.commit('dialog/loader/show');
                    api_order.buyerIndex().then(response => {
                        this.$store.commit('dialog/loader/hide');
                        if(!response) return;

                        // commit to auth/data/selfOrders vuex store module
                        this.$store.commit('auth/data/fill', {
                            key : 'selfOrders',
                            data: response.data.orders
                        });
                        this.ordersCacheCtr += 1;
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
    th, td {
        vertical-align: top !important;
    }
</style>
