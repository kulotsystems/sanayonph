<template>
    <v-app>
        <toolbar-main
            :custom-title="`@${$store.getters['store/user'].username}` + ($vuetify.breakpoint.smAndUp ? ' Product' : '')"
        />
        <v-main>
            <bg-white/>
            <v-container class="pa-2 pa-sm-3">
                <v-row v-if="product != null">
                    <!-- PRODUCT IMAGES -->
                    <v-col cols="12" sm="6" md="6" class="pa-0 pa-sm-5 pa-md-6">
                        <v-card :flat="$vuetify.breakpoint.xs">
                            <v-carousel elevation="1" class="rounded-t-sm rounded-b-sm" v-if="product.gen_images.length > 0 || product.var_images.length > 0" v-model="product.active_image" hide-delimiters show-arrows-on-hover :continuous="false" height="auto">
                                <v-carousel-item v-for="(image, i) in product.gen_images.concat(product.var_images)" :key="i">
                                    <v-img
                                        :lazy-src="i === 0 && $vuetify.breakpoint.xs ? `${$store.getters['path/productImg']['128']}/${image}` : $store.getters['path/defaultProduct']"
                                             :src="`${$store.getters['path/productImg']['300']}/${image}`"
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
                            <v-img v-else
                                   :src="$store.getters['path/defaultProduct']"
                                   aspect-ratio="1"
                            />
                        </v-card>
                    </v-col>

                    <!-- PRODUCT DATA -->
                    <v-col cols="12" sm="6" md="6" class="py-3 px-4 pa-sm-4 pa-md-5">
                        <v-card flat class="transparent">
                            <v-card-title class="pa-1 secondary--text">
                                <h4>{{ product.name }}</h4>
                            </v-card-title>
                            <v-card-subtitle class="pa-0 mt-3">

                                <!-- Product Stock and Price -->
                                <table style="width: 100%">
                                    <tr>
                                        <td class="primary--text" style="vertical-align: top">
                                            <div style="display: inline-block" align="center">
                                                <b>{{ Number(product.pricing.stock.total).toLocaleString() }}</b>
                                                Stock
                                            </div>
                                        </td>
                                        <td align="right">
                                            <h1 class="primary--text">
                                                <span class="font-weight-thin">&#8369;</span>
                                                <b>{{ Number(product.pricing.price.min).toLocaleString() }}</b>
                                                <template v-if="product.pricing.price.min !== product.pricing.price.max">
                                                    <span class="font-weight-thin">~</span> <b>{{ Number(product.pricing.price.max).toLocaleString() }}</b>
                                                </template>
                                            </h1>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Store Link -->
                                <store-link
                                    :avatar="$store.getters['store/avatar']"
                                    :username="$store.getters['store/user'].username"
                                    :full-name="$store.getters['store/user'].name.full_name_1"
                                    nav-back
                                    class="mt-4"
                                />

                                <!-- Product Details  -->
                                <div class="mt-4">
                                    <h4 class="primary--text">CATEGORY</h4>
                                    <p>
                                        {{ product.category.name }}
                                    </p>

                                    <h4 class="primary--text">DESCRIPTION</h4>
                                    <p style="white-space: pre-wrap;">{{ product.description }}</p>
                                </div>
                            </v-card-subtitle>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>

            <!-- BUY SHEET -->
            <v-bottom-sheet v-model="config.buying" persistent v-if="product != null" inset scrollable>
                <v-card>
                    <v-card-title class="px-4">
                        <span class="primary--text">Choose Variation & Quantity</span>
                        <v-spacer></v-spacer>
                        <v-btn icon @click="config.buying = false">
                            <v-icon>close</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-card-text class="px-4">
                        <!-- Product Overview -->
                        <product-overview
                            :image="productOverview.image"
                            :name="product.name"
                            :label="productOverview.label"
                            :price="productOverview.price"
                            :stock="productOverview.stock"
                            :quantity="request.quantity"
                            display-price
                        />

                        <!-- Variation Selectors -->
                        <div class="mt-3" v-if="product.price_stock_mode === 'var1_only' || product.price_stock_mode === 'both_vars'">
                            <h3 class="primary--text">{{ product.variations[0].title }}:</h3>
                            <v-chip
                                v-for="(item, index) in product.variations[0].items"
                                :key="item.id"
                                :color="request.var1_index === index || item.disabled ? 'primary' : ''"
                                :disabled="item.disabled"
                                :outlined="item.disabled"
                                class="ma-1"
                                @click="request.var1_index = index"
                            >
                                {{ item.label }}
                            </v-chip>
                        </div>
                        <v-divider class="my-4" v-if="product.price_stock_mode === 'both_vars'"></v-divider>
                        <div class="mt-3" v-if="product.price_stock_mode === 'var2_only' || product.price_stock_mode === 'both_vars'">
                            <h3 class="primary--text">{{ product.variations[1].title }}:</h3>
                            <v-chip
                                v-for="(item, index) in product.variations[1].items"
                                :key="item.id"
                                :color="request.var2_index === index || item.disabled ? 'primary' : ''"
                                :disabled="item.disabled"
                                :outlined="item.disabled"
                                class="ma-1"
                                @click="request.var2_index = index"
                            >
                                {{ item.label }}
                            </v-chip>
                        </div>

                        <v-divider class="my-4"></v-divider>

                        <!-- Quantity Selector -->
                        <div class="mt-3">
                            <h3 class="primary--text">QUANTITY:</h3>
                            <v-btn color="primary" text small @click="decreaseQuantity">
                                <v-icon>remove</v-icon>
                            </v-btn>
                            <v-text-field v-model.number="request.quantity" class="centered" type="number" style="display: inline-block; width: 60px;"/>
                            <v-btn color="primary" text small @click="increaseQuantity">
                                <v-icon>add</v-icon>
                            </v-btn>
                        </div>

                    </v-card-text>

                    <!-- Checkout Button -->
                    <v-card-actions>
                        <button-action
                            label="CHECKOUT"
                            color="yellow"
                            class-name="primary--text text-body-1"
                            :disabled="config.btnCheckout.disabled || productOverview.price == null || productOverview.stock == null || parseInt(productOverview.stock) <= 0 || request.quantity <= 0"
                            block
                            large
                            @click="checkout"
                        />
                    </v-card-actions>
                </v-card>
            </v-bottom-sheet>

            <!-- CHECKOUT SHEET -->
            <v-bottom-sheet v-model="config.checkingOut" persistent v-if="product != null" inset scrollable>
                <v-card>
                    <v-card-title class="px-4">
                        <span class="primary--text">Complete Order</span>
                        <v-spacer></v-spacer>
                        <v-btn icon @click="config.checkingOut = false" :style="{'visibility': config.btnPlaceOrder.loading ? 'hidden' : 'visible'}">
                            <v-icon>close</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-card-text class="px-4">

                        <!-- Product Overview -->
                        <v-list-item two-line :ripple="false">
                            <v-list-item-avatar rounded="0" class="rounded-t rounded-b">
                                <v-img v-if="productOverview.image"
                                       :lazy-src="$store.getters['path/defaultProduct']"
                                            :src="`${$store.getters['path/productImg']['064']}/${productOverview.image}`"
                                       aspect-ratio="1"
                                />
                                <v-img v-else
                                       :src="$store.getters['path/defaultProduct']"
                                       aspect-ratio="1"
                                />
                            </v-list-item-avatar>
                            <v-list-item-content>
                                <v-list-item-title>
                                    <span>{{ this.product.name }}</span>
                                    <br>
                                    <small class="primary--text">
                                        <template v-if="this.productOverview.label != null">{{ this.productOverview.label }}</template>
                                        <template v-else>&nbsp;</template>
                                    </small>
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <!-- Sale Computation -->
                        <sale-computation
                            :price="productOverview.price"
                            :quantity="request.quantity"
                            :price-after-quantity="priceAfterQuantity"
                            :delivery-method="deliveryMethod"
                            :shipping-fee-after-quantity="shippingFeeAfterQuantity"
                            :price-after-shipping="priceAfterShipping"
                            :service-fee-after-shipping="serviceFeeAfterShipping"
                            :price-after-service-fee="priceAfterServiceFee"
                        />

                        <!-- Checkout Form -->
                        <v-form ref="checkoutForm" class="mt-5">

                            <!-- Delivery Method -->
                            <v-row dense>
                                <v-col cols="12">
                                    <v-select
                                        label="Delivery Method"
                                        v-model="request.delivery_method"
                                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response, 'delivery_method')]"
                                        :items="finalDeliveryMethods"
                                        :item-text="item  => `${item.name}`"
                                        :item-value="item => `${item.id}`"
                                        :prepend-inner-icon="$store.getters['icon/state'].delivery"
                                        @change="deliveryMethodChange"
                                        :disabled="config.btnPlaceOrder.loading"
                                        dense
                                    />
                                </v-col>
                                <v-col cols="12" v-if="request.delivery_method === '2'" class="mt-0 text-subtitle-2">
                                    Your payment mode is set to <span class="primary--text">Gcash</span> and the seller will notify you where to pickup your order upon confirmation.
                                </v-col>
                            </v-row>

                            <!-- Delivery Address -->
                            <v-row dense v-if="request.delivery_method === '1'">
                                <v-col cols="12">
                                    <v-autocomplete
                                        label="Delivery Address"
                                        v-model="request.delivery_address"
                                        class="text-body-1 uppercase"
                                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response, 'delivery_address')]"
                                        @change="$store.dispatch('form/reset', [response, 'delivery_address']).then(r => response)"
                                        :items="config.deliveryAddresses"
                                        :item-text="item  => `${item.address}`"
                                        :item-value="item => `${item.id}`"
                                        autocomplete="off"
                                        :prepend-inner-icon="$store.getters['icon/state'].address"
                                        :disabled="config.btnPlaceOrder.loading || deliveryMethodIsNotAvailable"
                                        dense
                                    />
                                </v-col>
                            </v-row>

                            <!-- Payment Method -->
                            <v-row dense v-if="request.delivery_method === '1'">
                                <v-col cols="12">
                                    <v-select
                                        label="Payment Method"
                                        v-model="request.payment_method"
                                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response, 'payment_method')]"
                                        @change="$store.dispatch('form/reset', [response, 'payment_method']).then(r => response)"
                                        :items="config.paymentMethods"
                                        :item-text="item  => `${item.name}`"
                                        :item-value="item => `${item.id}`"
                                        :prepend-inner-icon="$store.getters['icon/state'].payment"
                                        :disabled="config.btnPlaceOrder.loading || deliveryMethodIsNotAvailable"
                                        dense
                                    />
                                </v-col>
                            </v-row>
                        </v-form>

                        <!-- Delivery Method Not Available -->
                        <p v-if="deliveryMethodIsNotAvailable" class="primary--text">
                            <v-icon small color="primary">info</v-icon>
                            Selected <b>Delivery Method</b> is not yet available.
                        </p>
                    </v-card-text>

                    <!-- Place Order Button -->
                    <v-card-actions>
                        <button-action
                            label="PLACE ORDER"
                            :icon="$store.getters['icon/state'].order"
                            color="yellow"
                            class-name="primary--text text-body-1"
                            :loading="config.btnPlaceOrder.loading"
                            :disabled="deliveryMethodIsNotAvailable"
                            block
                            large
                            @click="placeOrder"
                        />
                    </v-card-actions>
                </v-card>
            </v-bottom-sheet>

            <!-- BUY BUTTON -->
            <v-bottom-navigation v-if="product != null" class="block" grow app mandatory>
                <button-action
                    label="BUY NOW"
                    :icon="$store.getters['icon/state'].buy"
                    block
                    large
                    :loading="config.btnBuy.loading"
                    @click="buy"
                    color="yellow"
                    class-name="primary--text text-body-1"
                />
            </v-bottom-navigation>
        </v-main>
    </v-app>
</template>

<script>
    import api_store from '../../../api/api-store.js';
    import api_order from '../../../api/api-order.js';

    export default {
        name: 'ProfileGuestStoreProductsDetails',
        components: {
            'bg-white'        : () => import('../../../components/backgrounds/BackgroundWhite.vue'),
            'toolbar-main'    : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'button-action'   : () => import('../../../components/buttons/ButtonAction.vue'),
            'store-link'      : () => import('../../../components/sheets/StoreLink.vue'),
            'product-overview': () => import('../../../components/sheets/ProductOverview.vue'),
            'sale-computation': () => import('../../../components/sheets/SaleComputation.vue')
        },
        data() {
            return {
                config: {
                    deliveryAddresses: [],
                    deliveryMethods  : [],
                    paymentMethods   : [],
                    serviceFees      : [],
                    shippingFees     : [],
                    buying     : false,
                    checkingOut: false,
                    btnBuy: {
                        loading : false
                    },
                    btnCheckout: {
                        disabled: false
                    },
                    btnPlaceOrder: {
                        loading : false
                    }
                },
                request: {
                    var1_index: -1,
                    var2_index: -1,
                    quantity  : 1,
                    delivery_method  : 0,
                    delivery_address : 0,
                    payment_method   : 0,
                },
                response: {
                    message: '',
                    errors : {}
                },
                productCacheCTR: 0
            }
        },
        computed: {
            // computed product
            product() {
                let ctr = this.productCacheCTR;
                return this.$store.getters['store/products'][`${this.$route.params.username}_${this.$route.params.store}_${this.$route.params.product}`];
            },

            // computed product overview
            productOverview() {
                let overview = {
                    image: null,
                    price: null,
                    stock: null,
                    label: null
                };
                if(this.product.price_stock_mode === 'var1_only') {
                    if(this.request.var1_index >= 0) {
                        overview.image = this.product.variations[0].items[this.request.var1_index].image;
                        overview.label = this.product.variations[0].items[this.request.var1_index].label;
                        for(let i=0; i<this.product.prices_stocks.length; i++) {
                            let priceStock = this.product.prices_stocks[i];
                            if(priceStock.var1_item_index === this.request.var1_index) {
                                overview.price = priceStock.price;
                                overview.stock = priceStock.stock;
                                break;
                            }
                        }
                    }
                }
                else if(this.product.price_stock_mode === 'var2_only') {
                    if(this.request.var2_index >= 0) {
                        overview.image = this.product.variations[1].items[this.request.var2_index].image;
                        overview.label = this.product.variations[1].items[this.request.var2_index].label;
                        for(let i=0; i<this.product.prices_stocks.length; i++) {
                            let priceStock = this.product.prices_stocks[i];
                            if(priceStock.var2_item_index === this.request.var2_index) {
                                overview.price = priceStock.price;
                                overview.stock = priceStock.stock;
                                break;
                            }
                        }
                    }
                }
                else if(this.product.price_stock_mode === 'both_vars') {
                    if(this.request.var1_index >= 0 && this.request.var2_index >= 0) {
                        overview.image = this.product.variations[0].items[this.request.var1_index].image;
                        if(overview.image == null) overview.image = this.product.variations[1].items[this.request.var2_index].image;
                        overview.label = this.product.variations[0].items[this.request.var1_index].label + ', ' + this.product.variations[1].items[this.request.var2_index].label;
                        for(let i=0; i<this.product.prices_stocks.length; i++) {
                            let priceStock = this.product.prices_stocks[i];
                            if(priceStock.var1_item_index === this.request.var1_index && priceStock.var2_item_index === this.request.var2_index) {
                                overview.price = priceStock.price;
                                overview.stock = priceStock.stock;
                                break;
                            }
                        }
                    }
                }

                // verify image overview
                if(overview.image == null) {
                    if(this.product.gen_images.length > 0)
                        overview.image = this.product.gen_images[0];
                }

                return overview;
            },

            // computed final delivery methods
            finalDeliveryMethods() {
                let deliveryMethods = [];
                for(let i=0; i<this.config.deliveryMethods.length; i++) {
                    let deliveryMethod = this.config.deliveryMethods[i];
                    if(deliveryMethod.is_pickup) {
                        // confirm pickup
                        if(this.product != null) {
                            if(!this.product.allows_pickup)
                                continue;
                        }
                    }
                    deliveryMethods.push(deliveryMethod);
                }
                return deliveryMethods;
            },

            // computed price after multiplying with quantity
            priceAfterQuantity() {
                let price = 0;
                if(this.productOverview.price != null)
                    price = this.productOverview.price * this.request.quantity;
                return price;
            },

            // computed shipping fee after applying quantity
            shippingFeeAfterQuantity() {
                let fee = {
                    max_quantity: 0,
                    operation   : '',
                    amount      : 0
                };
                if(this.deliveryMethod != null) {
                    if(this.deliveryMethod.is_delivery) {
                        for(let i=0; i<this.config.shippingFees.length; i++) {
                            const shippingFee = this.config.shippingFees[i];
                            if(this.request.quantity <= shippingFee.max_quantity) {
                                fee.max_quantity = shippingFee.max_quantity;
                                fee.operation    = shippingFee.operation;
                                if(shippingFee.operation === '=')
                                    fee.amount = shippingFee.amount;
                                else if(shippingFee.operation === '*')
                                    fee.amount = shippingFee.amount * this.request.quantity;
                                break;
                            }
                        }
                    }
                }
                return fee;
            },

            // computed price after applying shipping fee
            priceAfterShipping() {
                return this.priceAfterQuantity + this.shippingFeeAfterQuantity.amount;
            },

            // computed service fee after applying shipping fee
            serviceFeeAfterShipping() {
                let fee = {
                    percent: 0,
                    amount : 0
                };
                for(let i=0; i<this.config.serviceFees.length; i++) {
                    const serviceFee = this.config.serviceFees[i];
                    if(this.priceAfterShipping <= serviceFee.max_order) {
                        fee.percent = serviceFee.percent;
                        fee.amount  = Math.round(this.priceAfterShipping * (serviceFee.percent / 100));
                        break;
                    }
                }
                return fee;
            },

            // computed price after applying service fee
            priceAfterServiceFee() {
                return this.priceAfterShipping - this.serviceFeeAfterShipping.amount;
            },

            // computed delivery method
            deliveryMethod() {
                let deliveryMethod = null;
                for(let i=0; i<this.config.deliveryMethods.length; i++) {
                    if(this.config.deliveryMethods[i].id.toString() === this.request.delivery_method) {
                        deliveryMethod = this.config.deliveryMethods[i];
                        break;
                    }
                }
                return deliveryMethod;
            },

            // computed payment method
            paymentMethod() {
                let paymentMethod = null;
                for(let i=0; i<this.config.paymentMethods.length; i++) {
                    if(this.config.paymentMethods[i].id.toString() === this.request.payment_method) {
                        paymentMethod = this.config.paymentMethods[i];
                        break;
                    }
                }
                return paymentMethod;
            },

            // computed delivery method is not available
            deliveryMethodIsNotAvailable() {
                let bool = false;
                if(this.deliveryMethod != null)
                    bool = this.deliveryMethod.is_delivery;
                return bool;
            }
        },
        methods : {
            /****************************************************************************************************
             * METHOD: DECREASE QUANTITY
             * Decrease quantity by 1
             */
            decreaseQuantity() {
                if(this.request.quantity > 1)
                    this.request.quantity -= 1;
            },

            /****************************************************************************************************
             * METHOD: INCREASE QUANTITY
             * Increase quantity by 1
             */
            increaseQuantity() {
                if(this.productOverview.stock != null) {
                    if(this.request.quantity < this.productOverview.stock)
                        this.request.quantity += 1;
                }
                else
                    this.request.quantity += 1;
            },

            /****************************************************************************************************
             * METHOD: BUY
             * Show product buy sheet after some checks
             */
            buy() {
                if(!this.config.btnBuy.loading) {
                    // check if authenticated
                    if(!this.$store.getters['auth/authenticated']) {
                        this.$store.commit('dialog/message/show', {
                            title : 'Notice',
                            prompt: 'Please <span class="primary--text">Sign In</span> first.',
                            okIcon : this.$store.getters['icon/state'].sign_in,
                            okLabel: 'Sign In',
                            callback: {
                                async : true,
                                action: () => {
                                    window.open(this.$router.history.base + '?next=' + encodeURI(this.$route.path), '_self');
                                }
                            }
                        });
                    }

                    // get additional product data and buying details
                    else if(this.config.deliveryAddresses.length <= 0 && this.config.deliveryMethods.length <=0 && this.config.paymentMethods.length <= 0) {
                        this.config.btnBuy.loading = true;
                        api_store.product(this.$route.params.product, 'buy').then(response => {
                            if(!response) return;

                            // commit product to vuex store module
                            this.$store.commit('store/addProduct', {
                                username  : this.$route.params.username,
                                store     : this.$route.params.store,
                                product   : response.data.product
                            });
                            this.productCacheCTR += 1;

                            // commit to other data to config
                            this.config.deliveryAddresses = response.data.delivery_addresses;
                            this.config.deliveryMethods   = response.data.delivery_methods;
                            this.config.paymentMethods    = response.data.payment_methods;
                            this.config.serviceFees       = response.data.service_fees;
                            this.config.shippingFees      = response.data.shipping_fees;
                            this.config.btnBuy.loading = false;
                            this.showBuySheet();
                        }).catch(errors => {
                            this.$store.commit('dialog/error/show', errors);
                            this.response.message = errors.response.data.message;
                            this.response.errors  = errors.response.data.errors;
                        });
                    }

                    // show buy sheet if everything is already fetched
                    else
                        this.showBuySheet();
                }
            },

            /****************************************************************************************************
             * METHOD: SHOW BUY SHEET
             * Show buy sheet after pressing buy button
             */
            showBuySheet() {
                if(this.config.deliveryAddresses.length <= 0) {
                    this.$store.commit('dialog/message/show', {
                        title : 'Notice',
                        prompt: 'Please set at least one <span class="primary--text">Delivery Addresss</span> first.',
                        okIcon : this.$store.getters['icon/state'].address,
                        okLabel: 'Addresses',
                        callback: {
                            action: () => {
                                this.$router.replace({ name: 'profile-delivery-addresses', params: { username: this.$store.getters['auth/user'].username } });
                            }
                        }
                    });
                }
                else {
                    this.config.buying = true;

                    // enable or disable variation items
                    if(this.product.price_stock_mode === 'var1_only' || this.product.price_stock_mode === 'var2_only') {
                        let index = this.product.price_stock_mode === 'var1_only' ? 0 : 1;
                        for(let i=0; i<this.product.variations[index].items.length; i++) {
                            let disabled = false;
                            for(let j=0; j<this.product.prices_stocks.length; j++) {
                                if(this.product.variations[index].items[i].index === this.product.prices_stocks[j][`var${index + 1}_item_index`]) {
                                    disabled = this.product.prices_stocks[j].stock <= 0;
                                    break;
                                }
                            }
                            this.product.variations[index].items[i].disabled = disabled;
                        }
                    }

                    // automatically select first available variations
                    if(this.request.var1_index < 0 && this.request.var2_index < 0) {
                        if(this.product.price_stock_mode === 'var1_only' || this.product.price_stock_mode === 'both_vars') {
                            for(let i=0; i<this.product.variations[0].items.length; i++) {
                                if(!this.product.variations[0].items[i].disabled) {
                                    this.request.var1_index = i;
                                    break;
                                }
                            }
                        }
                        if(this.product.price_stock_mode === 'var2_only' || this.product.price_stock_mode === 'both_vars') {
                            for(let i=0; i<this.product.variations[1].items.length; i++) {
                                if(!this.product.variations[1].items[i].disabled) {
                                    this.request.var2_index = i;
                                    break;
                                }
                            }
                        }
                    }
                }
            },

            /****************************************************************************************************
             * METHOD: CHECKOUT
             * Proceed to checkout
             */
            checkout() {
                if(this.productOverview.price != null && this.productOverview.stock != null) {
                    if(this.request.quantity > this.productOverview.stock) {
                        this.$store.commit('dialog/message/show', {
                            title : 'Unable to Checkout',
                            prompt: 'Your chosen quantity is greater than the available stock.'
                        });
                    }
                    else {
                        this.config.buying      = false;
                        this.config.checkingOut = true;
                    }
                }
            },

            /****************************************************************************************************
             * METHOD: PLACE ORDER
             * Complete order
             */
            placeOrder() {
                let checkoutForm = this.$refs.checkoutForm;
                if(!this.config.btnPlaceOrder.loading && checkoutForm.validate()) {
                    this.config.btnPlaceOrder.loading = true;

                    api_order.placeOrder(this.$route.params.product, {
                        ...this.request,
                        final_price: this.priceAfterServiceFee
                    }).then(response => {
                        if(!response) return;

                        if(response.data.ordered) {
                            this.config.checkingOut = false;
                            this.$store.commit('auth/data/purge', 'selfOrders');
                            this.$store.commit('dialog/message/show', {
                                title   : 'Order Placed!',
                                prompt  : 'You have successfully place an order for this product.',
                                okIcon  : this.$store.getters['icon/state'].order,
                                okLabel : 'View Order',
                                callback: {
                                    action: () => {
                                        this.$router.replace({
                                            name  : 'profile-orders-details',
                                            params: {
                                                username: this.$store.getters['auth/user'].username,
                                                order   : response.data.ordered.toString()
                                            }
                                        });
                                    }
                                }
                            });
                        }

                        this.config.btnPlaceOrder.loading = false;
                    }).catch(errors => {
                        this.config.btnPlaceOrder.loading = false;
                        this.$store.commit('dialog/error/show', {...errors, reload: true});
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                    });
                }
            },

            /****************************************************************************************************
             * METHOD: DELIVERY METHOD CHANGE
             * Set default payment method per delivery method
             */
            deliveryMethodChange() {
                this.$store.dispatch('form/reset', [this.response, 'delivery_method']).then(r => this.response);
                if(this.request.delivery_method === '2') {
                    // pickup
                    this.request.payment_method = '2';
                }
            },
        },
        mounted() {
            if(this.product == null) {
                this.$store.commit('dialog/loader/show');
                api_store.product(this.$route.params.product).then(response => {
                    this.$store.commit('dialog/loader/hide');
                    if(!response) return;

                    // commit product to vuex store module
                    this.$store.commit('store/addProduct', {
                        username  : this.$route.params.username,
                        store     : this.$route.params.store,
                        product   : response.data.product
                    });
                    this.productCacheCTR += 1;
                }).catch(errors => {
                    this.$store.commit('dialog/loader/hide');
                    this.$store.commit('dialog/error/show', errors);
                    this.response.message = errors.response.data.message;
                    this.response.errors  = errors.response.data.errors;
                });
            }
        }
    }
</script>

<style scoped>

</style>
