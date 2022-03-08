<template>
    <table style="width: 100%" class="mt-2">
        <tr>
            <td>ORIGINAL PRICE</td>
            <td align="right" class="primary--text">
                <big  v-if="price != null"><span class="font-weight-thin">&#8369;</span> {{ Number(price).toLocaleString() }}</big>
            </td>
        </tr>
        <tr>
            <td>QUANTITY</td>
            <td align="right" class="primary--text">
                <big>{{ Number(quantity).toLocaleString() }}</big>
            </td>
        </tr>
        <tr>
            <td>TOTAL PRICE</td>
            <td align="right" class="primary--text">
                <big>{{ Number(priceAfterQuantity).toLocaleString() }}</big>
            </td>
        </tr>
        <tr v-if="!inStore && deliveryMethod ? deliveryMethod.is_delivery: false">
            <td>SHIPPING FEE</td>
            <td align="right" class="primary--text">
                <big>{{ Number(shippingFeeAfterQuantity.amount).toLocaleString() }}</big>
            </td>
        </tr>
        <tr>
            <td>ORDER {{ totalLabel }}</td>
            <td align="right" class="primary--text">
                <big><b><span class="font-weight-thin">&#8369;</span> {{ Number(priceAfterShipping).toLocaleString() }}</b></big>
            </td>
        </tr>
        <tr v-if="inStore && serviceFeeAfterShipping">
            <td>TRANSACTION FEE <small style="opacity: 0.9">(about {{ serviceFeeAfterShipping.percent }}%)</small></td>
            <td align="right" class="primary--text">
                <big>{{ Number(serviceFeeAfterShipping.amount).toLocaleString() }}</big>
            </td>
        </tr>
    </table>
</template>

<script>
    export default {
        name: 'SaleComputation',
        components: {},
        props: {
            price: {
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
            totalLabel: {
                type   : String,
                default: 'TOTAL'
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
        methods : {}
    }
</script>

<style scoped>

</style>
