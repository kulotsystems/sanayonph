<template>
    <table style="width: 100%" class="mt-2">
        <!-- PAYMENT METHOD -->
        <tr>
            <td>PAYMENT METHOD</td>
            <td v-if="paymentMethod" align="right" class="primary--text">
                {{ paymentMethod.name }}
            </td>
        </tr>

        <!-- PAYMENT INSTRUCTIONS / SCREENSHOT UPLOAD -->
        <tr v-if="!inStore && paymentMethod.is_gcash">
            <td colspan="2" class="py-3">
                <v-row>
                    <v-col cols="12" sm="6" class="mb-0">
                        <p>PAYMENT INSTRUCTIONS</p>
                        <ol>
                            <li>
                                <p>
                                    Please send your payment to the following <span class="primary--text">Gcash</span> account:
                                </p>
                                <table class="mb-4" style="width: 100%;">
                                    <tr>
                                        <td>Number</td>
                                        <td class="px-2">:</td>
                                        <td class="primary--text">{{ config.gcash.number }}</td>
                                    </tr>
                                    <tr>
                                        <td>Name</td>
                                        <td class="px-2">:</td>
                                        <td class="primary--text">{{ config.gcash.name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Amount</td>
                                        <td class="px-2">:</td>
                                        <td class="primary--text"><span class="font-weight-thin">&#8369; {{ Number(orderTotal).toLocaleString() }}</span> </td>
                                    </tr>
                                </table>
                            </li>
                            <li>
                                <p>
                                    Upload a <span class="primary--text">screenshot</span> of your payment confirmation screen or SMS.
                                </p>
                            </li>
                        </ol>
                    </v-col>
                    <v-col cols="12" sm="6">
                        <v-row class="justify-center">
                            <v-col cols="6" sm="12">
                                <v-card>
                                    <v-img
                                        :lazy-src="config.paymentPortrait"
                                             :src="paymentStatus.screenshot != null ? $store.getters['path/paymentScreenshot'] + '/' + paymentStatus.screenshot : config.paymentPortrait"
                                        aspect-ratio="0.56"
                                    />
                                    <v-card-actions v-if="!paymentConfirmed">
                                        <v-spacer></v-spacer>
                                        <v-btn fab class="primary lighten-1" depressed x-small @click="$store.commit('dialog/cropper/show')">
                                            <v-icon>insert_photo</v-icon>
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </td>
        </tr>

        <!-- PAYMENT STATUS -->
        <template v-if="paymentStatus != null">
            <tr>
                <td>PAYMENT STATUS</td>
                <td align="right" class="primary--text">
                    {{ paymentStatus.status }}
                </td>
            </tr>
            <tr v-if="paymentStatus.date_time != null">
                <td>DATE & TIME</td>
                <td align="right" class="primary--text">
                    {{ paymentStatus.date_time }}
                </td>
            </tr>
            <tr v-if="paymentStatus.remarks != null">
                <td colspan="2">
                    REMARKS
                    <p class="primary--text">{{ paymentStatus.remarks }}</p>
                </td>
            </tr>
        </template>
    </table>
</template>

<script>
    import payment_portrait from '../../assets/img/payment-portrait.png';

    export default {
        name: 'PaymentOverview',
        components: {},
        props: {
            paymentMethod: {
                type   : Object,
                default: null
            },
            paymentStatus: {
                type   : Object,
                default: null
            },
            orderTotal: {
                type   : Number,
                default: 0
            },
            inStore: {
                type   : Boolean,
                default: false
            }
        },
        data() {
            return {
                config: {
                    paymentPortrait: payment_portrait,
                    gcash: {
                        number: '09514270377',
                        name  : 'Justin Joy D.'
                    }
                }
            }
        },
        computed: {
            // computed payment confirmed or not
            paymentConfirmed() {
                return this.paymentStatus != null ? this.paymentStatus.status === 'Confirmed' : false;
            }
        },
        methods : {}
    }
</script>

<style scoped>
    th, td {
        vertical-align: top;
        padding-top: 5px;
        padding-bottom: 5px;
        border-bottom: 2px dotted #ddd;
    }
    tr:last-child th,
    tr:last-child td {
        padding-bottom: 0;
        border-bottom: 0;
    }
</style>
