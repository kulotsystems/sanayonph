<template>
    <v-app>
        <dialogs/>
        <dialog-cropper :width="259" :height="460" :maxWidth="300" plain @crop="uploadPhoto"/>
        <toolbar-main></toolbar-main>
        <v-main class="yellow">
            <bg-yellow/>
            <v-container class="pa-2 pa-sm-3">
                <v-row v-if="config.order != null">
                    <!-- LEFT COLUMN -->
                    <v-col cols="12" sm="6" md="6" class="pb-0" :class="{'pb-3': this.orderCancelled || this.orderDeclined}">
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

                                <!-- Store Link -->
                                <store-link
                                    :avatar="config.order.seller.avatar"
                                    :username="config.order.seller.username"
                                    :full-name="config.order.seller.name.full_name_1"
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
                                        class="my-3"
                                    />
                                    <v-divider></v-divider>
                                </div>

                                <!-- Order Total -->
                                <order-total :total="config.order.total" class="pa-3"/>

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

            <!-- CANCEL ORDER SHEET -->
            <v-bottom-sheet v-model="config.cancelling" v-if="config.order != null" persistent inset scrollable>
                <v-card>
                    <v-card-title class="px-4">
                        <span class="primary--text">Cancel Order</span>
                        <v-spacer></v-spacer>
                        <v-btn icon @click="config.cancelling = false" :style="{'visibility': config.btnCancel.loading ? 'hidden' : 'visible'}">
                            <v-icon>close</v-icon>
                        </v-btn>
                    </v-card-title class="px-4">
                    <v-card-text>
                        <template v-if="paymentConfirmed">
                            <p class="text-subtitle-2">
                                Sorry, you cannot cancel an order when <span class="primary--text">payment</span> is already <span class="primary--text">confirmed</span>.
                            </p>
                        </template>
                        <template v-else>
                            <p class="text-subtitle-2">You are about to cancel this order.</p>
                            <p class="text-subtitle-2">Please tell us why:</p>
                            <v-form ref="cancelOrderForm" @submit="cancelOrder">
                                <v-row dense>
                                    <v-col cols="12">
                                        <v-textarea
                                            label="Remarks"
                                            v-model="request.cancel_remarks"
                                            :counter="config.cancel_remarks.max"
                                            :rules="[$store.state.form.rules.required, $store.state.form.rules.max_chars(request.cancel_remarks, config.cancel_remarks.max), $store.state.form.rules.feedback(response, 'cancel_remarks')]"
                                            @keyup="$store.dispatch('form/reset', [response, 'cancel_remarks']).then(r => response)"
                                            class="text-body-1 uppercase"
                                            :disabled="config.btnCancel.loading"
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
                            label="CANCEL ORDER"
                            color="yellow"
                            icon="close"
                            class-name="primary--text text-body-1"
                            :loading="config.btnCancel.loading"
                            block
                            large
                            @click="cancelOrder"
                        />
                    </v-card-actions>
                </v-card>
            </v-bottom-sheet>

            <!-- REVIEW ORDER SHEET -->
            <v-bottom-sheet v-model="config.reviewing" v-if="config.order != null" persistent inset scrollable>
                <v-card>
                    <v-card-title>
                        <span class="primary--text">Review Order</span>
                        <v-spacer></v-spacer>
                        <v-btn icon @click="config.reviewing = false" :style="{'visibility': config.btnReview.loading ? 'hidden' : 'visible'}">
                            <v-icon>close</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-card-text>

                    </v-card-text>
                    <v-card-actions>
                        <button-action
                            label="SUBMIT REVIEW"
                            color="yellow"
                            icon="close"
                            class-name="primary--text text-body-1"
                            :loading="config.btnReview.loading"
                            block
                            large
                            @click="reviewOrder"
                        />
                    </v-card-actions>
                </v-card>
            </v-bottom-sheet>

            <!-- ORDER CONFIRM/RECEIVE ACTIONS -->
            <template v-if="config.order != null">
                <v-bottom-navigation v-if="!orderDeclined && !orderCancelled && !orderCompleted" class="block" grow app>
                    <button-action
                        label="Cancel Order"
                        icon="cancel_presentation"
                        class="text-body-1"
                        :class="{'blurred': paymentConfirmed}"
                        :loading="config.btnCancelActivator.loading"
                        rounded
                        large
                        text
                        @click="attempt(false)"
                    />
                    <button-action
                        label="Order Received"
                        icon="done_outline"
                        class="text-body-1"
                        :class="{'blurred': !orderForPickup}"
                        :loading="config.btnReceiveActivator.loading"
                        rounded
                        large
                        text
                        @click="attempt(true)"
                    />
                </v-bottom-navigation>
            </template>
        </v-main>
    </v-app>
</template>

<script>
    import api_order from '../../../api/api-order.js';

    export default {
        name: 'ProfileSelfOrdersOLDDetails',
        components: {
            'bg-yellow'        : () => import('../../../components/backgrounds/BackgroundYellow.vue'),
            'dialogs'          : () => import('../../../components/dialogs/Dialogs.vue'),
            'dialog-cropper'   : () => import('../../../components/dialogs/DialogCropper.vue'),
            'toolbar-main'     : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'store-link'       : () => import('../../../components/sheets/StoreLink.vue'),
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
                    order: null,
                    cancelling : false,
                    reviewing  : false,
                    cancel_remarks: {
                        max: 300
                    },
                    review_remarks: {
                        max: 300
                    },
                    btnCancelActivator: {
                        loading: false
                    },
                    btnReceiveActivator: {
                        loading: false
                    },
                    btnCancel: {
                        loading: false
                    },
                    btnReview: {
                        loading: false
                    }
                },
                request: {
                    cancel_remarks: '',
                    review_remarks: ''
                },
                response: {
                    message: '',
                    errors : {}
                }
            }
        },
        computed: {
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

            // computed order for pickup
            orderForPickup() {
                return this.config.order.status.order != null ? this.config.order.status.order.status === 'For pickup / meetup' : false;
            },

            // computed order completed
            orderCompleted() {
                return this.config.order.status.order != null ? this.config.order.status.order.status === 'Completed' : false;
            }
        },
        methods : {
            /****************************************************************************************************
             * METHOD: ATTEMPT CANCEL / RECEIVE
             * Fetch fresh order details, then attempt to cancel or receive order
             */
            attempt(receiving) {
                if((!receiving && !this.config.btnCancelActivator.loading) || (receiving && !this.config.btnReceiveActivator.loading)) {
                    if(!receiving)
                        this.config.btnCancelActivator.loading = true;
                    else
                        this.config.btnReceiveActivator.loading = true;
                    api_order.buyerShow(this.$route.params.order).then(response => {
                        if(!response) return;

                        this.config.order = response.data.order;
                        this.$store.commit('auth/data/purge', 'selfOrders');
                        if(!receiving) {
                            this.config.btnCancelActivator.loading = false;
                            this.config.cancelling = true;
                        }
                        else {
                            this.config.btnReceiveActivator.loading = false;
                            this.receiveOrder();
                        }
                    }).catch(errors => {
                        if(!receiving)
                            this.config.btnCancelActivator.loading = false;
                        else
                            this.config.btnReceiveActivator.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                    });
                }
            },


            /****************************************************************************************************
             * METHOD: UPLOAD PHOTO
             * Handle emitted image upload event
             */
            uploadPhoto(formData) {
                api_order.screenshot(this.$route.params.order, formData).then(response => {
                    if(!response) return;

                    this.config.order.status.payment = response.data.payment_status;
                    this.$store.commit('dialog/cropper/hide');
                }).catch(errors => {
                    this.$store.commit('dialog/cropper/load', false);
                    this.$store.commit('dialog/error/show', {...errors, reload: true});
                    this.response.message = errors.response.data.message;
                    this.response.errors  = errors.response.data.errors;
                });
            },

            /****************************************************************************************************
             * METHOD: CANCEL ORDER
             * Cancel the order
             */
            cancelOrder() {
                let cancelOrderForm = this.$refs.cancelOrderForm;
                if(!this.config.btnCancel.loading && cancelOrderForm.validate()) {
                    this.config.btnCancel.loading = true;
                    api_order.buyerCancel(this.$route.params.order, {
                        remarks: this.request.cancel_remarks
                    }).then(response => {
                        if(!response) return;

                        if(response.data.status) {
                            this.config.order.status = response.data.status;
                            this.$store.commit('auth/data/purge', 'selfOrders');
                            this.config.cancelling = false;
                        }
                        this.config.btnCancel.loading = false;
                    }).catch(errors => {
                        this.config.btnCancel.loading = false;
                        this.$store.commit('dialog/error/show', {...errors, reload: true});
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                    });
                }

            },

            /****************************************************************************************************
             * METHOD: RECEIVE ORDER
             * Receive the order
             */
            receiveOrder() {
                if(!this.orderForPickup) {
                    this.$store.commit('dialog/message/show', {
                        title : 'Notice',
                        prompt: 'Sorry, you can not receive an order if it\'s not yet available <span class="primary--text">for pickup</span>.'
                    });
                }
                else {
                    this.$store.commit('dialog/confirm/show', {
                        title : 'Order Received',
                        prompt: 'Did you already received this order?',
                        yesCallback: {
                            async: true,
                            action: () => {
                                api_order.buyerReceive(this.$route.params.order).then(response => {
                                    if(!response) return;

                                    if(response.data.status) {
                                        this.config.order.status = response.data.status;
                                        this.$store.commit('auth/data/purge', 'selfOrders');
                                        this.$store.commit('dialog/confirm/hide');
                                    }
                                }).catch(errors => {
                                    this.config.btnCancel.loading = false;
                                    this.$store.commit('dialog/confirm/errors', errors);
                                    this.response.message = errors.response.data.message;
                                    this.response.errors  = errors.response.data.errors;
                                });
                            }
                        }
                    });
                }
            },

            /****************************************************************************************************
             * METHOD: REVIEW ORDER
             * Review order after receive
             */
            reviewOrder() {
                alert('NO CODE HERE YET.');
            }
        },
        created() {
            this.$store.commit('dialog/loader/show');
            api_order.buyerShow(this.$route.params.order).then(response => {
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
