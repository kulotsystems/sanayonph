<template>
    <v-app>
        <dialogs/>
        <toolbar-main/>
        <v-main class="secondary">
            <bg-secondary/>
            <v-container class="pa-2 pa-sm-3">
                <v-row v-if="config.order != null">
                    <!-- LEFT COLUMN -->
                    <v-col cols="12" sm="6" md="6" class="pb-0">
                        <!-- Order Status -->
                        <v-card>
                            <v-toolbar class="grey lighten-5" dense flat>
                                <v-icon class="grey--text text--lighten-1 mr-1" small>{{ $store.getters['icon/state'].order }}</v-icon>
                                <span class="v-toolbar-text text-body font-weight-light grey--text">Order</span>
                            </v-toolbar>

                            <v-card-text class="pa-0">
                                <!-- Order Overview -->
                                <order-overview
                                    :order-id="config.order.id"
                                    :date-time="config.order.ordered_at_str"
                                    :order-status="config.order.status.order"
                                    class="pa-3 pb-0"
                                />

                                <!-- Buyer Link -->
                                <buyer-link
                                    :avatar="config.order.buyer.avatar"
                                    :username="config.order.buyer.username"
                                    :full-name="config.order.buyer.name.full_name_1"
                                    class="mt-4"
                                />

                                <!-- Product Sale -->
                                <div v-for="sale in config.order.sales" :key="sale.id" class="px-3 pb-0">
                                    <product-sale
                                        :image="sale.product_image"
                                        :name="sale.product_name"
                                        :label="sale.product_label"
                                        :price="sale.product_price"
                                        :quantity="sale.quantity"
                                        :price-after-quantity="sale.price_after_quantity"
                                        :delivery-method="config.order.delivery_method"
                                        :shipping-fee-after-quantity="{amount: sale.shipping_fee_after_quantity}"
                                        :price-after-shipping="sale.price_after_shipping"
                                        :service-fee-after-shipping="{percent:sale.service_fee_percentage,  amount: sale.service_fee_after_shipping}"
                                        :price-after-service-fee="sale.price_after_service_fee"
                                        in-store
                                        class="my-3"
                                    />
                                    <v-divider></v-divider>
                                </div>

                                <!-- Order Total -->
                                <order-total :total="config.order.total_after_service_fee" class="pa-3"/>

                            </v-card-text>
                        </v-card>
                    </v-col>

                    <!-- RIGHT COLUMN -->
                    <v-col v-if="!orderDeclined && !orderCancelled" cols="12" sm="6" md="6" class="pb-0">
                        <!-- Payment Status -->
                        <v-card class="mb-3">
                            <v-toolbar class="grey lighten-5" dense flat>
                                <v-icon class="grey--text text--lighten-1 mr-1" small>{{ $store.getters['icon/state'].payment }}</v-icon>
                                <span class="v-toolbar-text text-body font-weight-light grey--text">Payment</span>
                            </v-toolbar>
                            <v-card-text>
                                <payment-overview
                                    :payment-method="config.order.payment_method"
                                    :payment-status="config.order.status.payment"
                                    :order-total="config.order.total"
                                    in-store
                                />
                            </v-card-text>
                        </v-card>

                        <!-- Delivery Status -->
                        <v-card class="mb-3">
                            <v-toolbar class="grey lighten-5" dense flat>
                                <v-icon class="grey--text text--lighten-1 mr-1" small>{{ $store.getters['icon/state'].delivery }}</v-icon>
                                <span class="v-toolbar-text text-body font-weight-light grey--text">Delivery</span>
                            </v-toolbar>
                            <v-card-text>
                                <delivery-overview
                                    :delivery-method="config.order.delivery_method"
                                    :delivery-status="config.order.status.delivery"
                                />
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>

            <!-- DECLINE ORDER SHEET -->
            <v-bottom-sheet v-model="config.declining" v-if="config.order != null" persistent inset scrollable>
                <v-card>
                    <v-card-title>
                        <span class="primary--text">Decline Order</span>
                        <v-spacer></v-spacer>
                        <v-btn icon @click="config.declining = false" :style="{'visibility': config.btnDecline.loading ? 'hidden' : 'visible'}">
                            <v-icon>close</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-card-text>
                        <template v-if="paymentConfirmed">
                            <p class="text-subtitle-2">
                                Sorry, you cannot decline an order when <span class="primary--text">payment</span> is already <span class="primary--text">confirmed</span>.
                            </p>
                        </template>
                        <template v-else>
                            <p class="text-subtitle-2">You are about to decline this order.</p>
                            <p class="text-subtitle-2">Please tell us why:</p>
                            <v-form ref="declineOrderForm" @submit="declineOrder">
                                <v-row dense>
                                    <v-col cols="12">
                                        <v-textarea
                                            label="Remarks"
                                            v-model="request.decline_remarks"
                                            :counter="config.decline_remarks.max"
                                            :rules="[$store.state.form.rules.required, $store.state.form.rules.max_chars(request.decline_remarks, config.decline_remarks.max), $store.state.form.rules.feedback(response, 'decline_remarks')]"
                                            @keyup="$store.dispatch('form/reset', [response, 'decline_remarks']).then(r => response)"
                                            class="text-body-1 uppercase"
                                            :disabled="config.btnDecline.loading"
                                            autocomplete="off"
                                            auto-grow
                                            outlined
                                        />
                                    </v-col>
                                </v-row>
                            </v-form>
                        </template>
                    </v-card-text>
                    <v-card-actions>
                        <button-action
                            v-if="!paymentConfirmed"
                            label="DECLINE ORDER"
                            color="yellow"
                            icon="close"
                            class-name="primary--text text-body-1"
                            :loading="config.btnDecline.loading"
                            block
                            large
                            @click="declineOrder"
                        />
                    </v-card-actions>
                </v-card>
            </v-bottom-sheet>

            <!-- CONFIRM ORDER SHEET -->
            <v-bottom-sheet v-model="config.confirming" v-if="config.order != null" persistent inset scrollable>
                <v-card>
                    <v-card-title class="px-4">
                        <span class="primary--text">Confirm Order</span>
                        <v-spacer></v-spacer>
                        <v-btn icon @click="config.confirming = false" :style="{'visibility': config.btnConfirm.loading ? 'hidden' : 'visible'}">
                            <v-icon>close</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-card-text class="px-4">
                        <template v-if="!paymentConfirmed">
                            <p class="text-subtitle-2">
                                Sorry, you cannot confirm an order when <span class="primary--text">payment</span> is <span class="primary--text">not yet confirmed</span>.
                            </p>
                        </template>
                        <template v-else>
                            <p class="text-subtitle-2">You are about to confirm an order.</p>
                            <p class="text-subtitle-2">Please enter the location and schedule for pickup or meetup.</p>
                            <v-form ref="confirmOrderForm" @submit="confirmOrder">
                                <!-- Pickup Location -->
                                <v-row dense>
                                    <v-col cols="12">
                                        <v-text-field
                                            label="Pickup / Meetup Location"
                                            v-model="request.pickup_location"
                                            :counter="config.pickup_location.max"
                                            :rules="[$store.state.form.rules.required, $store.state.form.rules.max_chars(request.pickup_location, config.pickup_location.max), $store.state.form.rules.feedback(response, 'pickup_location')]"
                                            @keyup="$store.dispatch('form/reset', [response, 'pickup_location']).then(r => response)"
                                            :disabled="config.btnConfirm.loading"
                                            class="uppercase"
                                            autocomplete="off"
                                            outlined
                                        />
                                    </v-col>
                                </v-row>

                                <v-row dense>
                                    <!-- Pickup Date -->
                                    <v-col cols="12" sm="6">
                                        <v-dialog
                                            ref="pickupDate"
                                            v-model="config.pickup_date.modal"
                                            :return-value.sync="request.pickup_date"
                                            persistent
                                            width="290px"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    label="Pickup Date"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    :value="formattedPickupDate"
                                                    :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'pickup_date')]"
                                                    @blur="$store.dispatch('form/reset', [response, 'pickup_date']).then(r => response)"
                                                    readonly
                                                    outlined
                                                />
                                            </template>
                                            <v-date-picker v-model="request.pickup_date" scrollable :min="minimumPickupDate">
                                                <v-spacer></v-spacer>
                                                <v-btn text color="primary" @click="config.pickup_date.modal = false">CANCEL</v-btn>
                                                <v-btn text color="primary" @click="$refs.pickupDate.save(request.pickup_date)">OK</v-btn>
                                            </v-date-picker>
                                        </v-dialog>
                                    </v-col>

                                    <!-- Pickup Time -->
                                    <v-col cols="12" sm="6">
                                        <v-dialog
                                            ref="pickupTime"
                                            v-model="config.pickup_time.modal"
                                            :return-value.sync="request.pickup_time"
                                            persistent
                                            width="290px"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    label="Pickup Time"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    :value="formattedPickupTime"
                                                    :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'pickup_time')]"
                                                    @blur="$store.dispatch('form/reset', [response, 'pickup_time']).then(r => response)"
                                                    readonly
                                                    outlined
                                                />
                                            </template>
                                            <v-time-picker v-model="request.pickup_time" scrollable ampm-in-title>
                                                <v-spacer></v-spacer>
                                                <v-btn text color="primary" @click="config.pickup_time.modal = false">CANCEL</v-btn>
                                                <v-btn text color="primary" @click="$refs.pickupTime.save(request.pickup_time)">OK</v-btn>
                                            </v-time-picker>
                                        </v-dialog>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </template>
                    </v-card-text>
                    <v-card-actions>
                        <button-action
                            v-if="paymentConfirmed"
                            label="CONFIRM ORDER"
                            color="yellow"
                            icon="check"
                            class-name="primary--text text-body-1"
                            :loading="config.btnConfirm.loading"
                            block
                            large
                            @click="confirmOrder"
                        />
                    </v-card-actions>
                </v-card>
            </v-bottom-sheet>

            <!-- ORDER DECLINE/CONFIRM ACTIONS -->
            <template v-if="config.order != null">
                <v-bottom-navigation v-if="!orderDeclined && !orderCancelled && !orderConfirmed && !orderForPickup" class="block" grow app>
                    <button-action
                        label="Decline Order"
                        class="text-body-1"
                        :class="{'blurred': paymentConfirmed}"
                        rounded
                        large
                        text
                        @click="config.declining = true"

                    />
                    <button-action
                        label="Confirm Order"
                        class="text-body-1"
                        :class="{'blurred': !paymentConfirmed}"
                        rounded
                        large
                        text
                        @click="config.confirming=true"
                    />
                </v-bottom-navigation>
            </template>
        </v-main>
    </v-app>
</template>

<script>
    import api_order from '../../../api/api-order.js';
    import format from 'date-fns/format';

    export default {
        name: 'ProfileSelfStoreOrdersOLDDetails',
        components: {
            'bg-secondary'     : () => import('../../../components/backgrounds/BackgroundSecondary.vue'),
            'dialogs'          : () => import('../../../components/dialogs/Dialogs.vue'),
            'toolbar-main'     : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'buyer-link'       : () => import('../../../components/sheets/BuyerLink.vue'),
            'order-overview'   : () => import('../../../components/sheets/OrderOverview.vue'),
            'product-sale'     : () => import('../../../components/sheets/ProductSale.vue'),
            'order-total'      : () => import('../../../components/sheets/OrderTotal.vue'),
            'payment-overview' : () => import('../../../components/sheets/PaymentOverview.vue'),
            'delivery-overview': () => import('../../../components/sheets/DeliveryOverview.vue'),
            'button-action'    : () => import('../../../components/buttons/ButtonAction.vue')
        },
        data() {
            return {
                config: {
                    order     : null,
                    declining : false,
                    confirming: false,
                    decline_remarks: {
                        max: 300
                    },
                    pickup_location: {
                        max: 300
                    },
                    pickup_date: {
                        modal: false
                    },
                    pickup_time: {
                        modal: false
                    },
                    btnDecline: {
                        loading: false
                    },
                    btnConfirm: {
                        loading: false
                    }
                },
                request: {
                    decline_remarks: '',
                    pickup_location: '',
                    pickup_date    : null,
                    pickup_time    : null
                },
                response: {
                    message: '',
                    errors : {}
                }
            }
        },
        computed: {
            // minimum pickup date
            minimumPickupDate() {
                return format(new Date(), 'yyyy-LL-dd');
            },

            // formatted pickup date
            formattedPickupDate() {
                return this.request.pickup_date ? format(new Date(this.request.pickup_date), 'MMMM d, yyy') : '';
            },

            // formatted pickup time
            formattedPickupTime() {
                let time = '';
                if(this.request.pickup_time) {
                    let t = this.request.pickup_time.split(':');
                    if(t.length === 2)
                        time = format(new Date(1970, 0, 1, parseInt(t[0]), parseInt(t[1])), 'h:m aa');
                }
                return time;
            },

            // computed payment confirmed or not
            paymentConfirmed() {
                return this.config.order.status.payment != null ? this.config.order.status.payment.status === 'Confirmed' : false;
            },

            // computed order declined
            orderDeclined() {
                return this.config.order.status.order != null ? this.config.order.status.order.status === 'Declined by seller' : false;
            },

            // computed order cancelled
            orderCancelled() {
                return this.config.order.status.order != null ? this.config.order.status.order.status === 'Cancelled by buyer' : false;
            },

            // computed order confirmed
            orderConfirmed() {
                return this.config.order.status.order != null ? this.config.order.status.order.status === 'Confirmed by seller' : false;
            },

            // computed order for pickup
            orderForPickup() {
                return this.config.order.status.order != null ? this.config.order.status.order.status === 'For pickup / meetup' : false;
            }
        },
        methods : {
            /****************************************************************************************************
             * METHOD: DECLINE ORDER
             * Decline the order
             */
            declineOrder() {
                let declineOrderForm = this.$refs.declineOrderForm;
                if(!this.config.btnDecline.loading && declineOrderForm.validate()) {
                    this.config.btnDecline.loading = true;
                    api_order.sellerDecline(this.$route.params.order, {
                        remarks: this.request.decline_remarks
                    }).then(response => {
                        if(!response) return;

                        if(response.data.status) {
                            this.config.order.status = response.data.status;
                            this.$store.commit('auth/data/purge', 'storeOrders');
                            this.config.declining = false;
                        }

                        this.config.btnDecline.loading = false;
                    }).catch(errors => {
                        this.config.btnDecline.loading = false;
                        this.$store.commit('dialog/error/show', {...errors, reload: true});
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                    })
                }
            },

            /****************************************************************************************************
             * METHOD: CONFIRM ORDER
             * Confirm the order
             */
            confirmOrder() {
                let confirmOrderForm = this.$refs.confirmOrderForm;
                if(!this.config.btnConfirm.loading && confirmOrderForm.validate()) {
                    this.config.btnConfirm.loading = true;
                    api_order.sellerConfirm(this.$route.params.order, {
                        pickup_location: this.request.pickup_location,
                        pickup_date    : this.request.pickup_date,
                        pickup_time    : this.request.pickup_time
                    }).then(response => {
                        if(!response) return;

                        if(response.data.status) {
                            this.config.order.status = response.data.status;
                            this.$store.commit('auth/data/purge', 'storeOrders');
                            this.config.confirming = false;
                        }

                        this.config.btnConfirm.loading = false;
                    }).catch(errors => {
                        this.config.btnConfirm.loading = false;
                        this.$store.commit('dialog/error/show', {...errors, reload: true});
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                    })
                }
            }
        },
        created() {
            this.$store.commit('dialog/loader/show');
            api_order.sellerShow(this.$route.params.order).then(response => {
                this.$store.commit('dialog/loader/hide');
                if(!response) return;

                this.config.order = response.data.order;
            }).catch(errors => {
                this.$store.commit('dialog/loader/hide');
                this.$store.commit('dialog/error/show', errors);
                this.response.message = errors.response.data.message;
                this.response.errors  = errors.response.data.errors;
            });
        }
    }
</script>

<style scoped>

</style>
