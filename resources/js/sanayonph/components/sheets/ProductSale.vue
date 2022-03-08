<template>
    <div>
        <!-- Product Overview  -->
        <product-overview
            :image="image"
            :name="name"
            :label="label"
            :price="price"
            :quantity="quantity"
        />

        <!-- Sale Computation -->
        <sale-computation
            :price="price"
            :quantity="quantity"
            :price-after-quantity="priceAfterQuantity"
            :delivery-method="deliveryMethod"
            :shipping-fee-after-quantity="shippingFeeAfterQuantity"
            :price-after-shipping="priceAfterShipping"
            :service-fee-after-shipping="serviceFeeAfterShipping"
            :price-after-service-fee="priceAfterServiceFee"
            total-label="SUBTOTAL"
            :in-store="inStore"
        />
    </div>
</template>

<script>
    export default {
        name: 'ProductSale',
        components: {
            'product-overview': () => import('./ProductOverview.vue'),
            'sale-computation': () => import('./SaleComputation.vue')
        },
        props: {
            image: {
                type   : String,
                default: null
            },
            name: {
                type   : String,
                default: ''
            },
            label: {
                type   : String,
                default: ''
            },
            price: {
                type   : Number,
                default: null
            },
            stock: {
                type   : Number,
                default: null
            },
            quantity: {
                type   : Number,
                default: null
            },
            priceAfterQuantity: {
                type   : Number,
                default: null
            },
            deliveryMethod: {
                type   : Object,
                default: null
            },
            shippingFeeAfterQuantity: {
                type   : Object,
                default: null
            },
            priceAfterShipping: {
                type   : Number,
                default: 0
            },
            serviceFeeAfterShipping: {
                type   : Object,
                default: null
            },
            priceAfterServiceFee: {
                type   : Number,
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
        computed: {},
        methods: {}
    }
</script>

<style scoped>

</style>
