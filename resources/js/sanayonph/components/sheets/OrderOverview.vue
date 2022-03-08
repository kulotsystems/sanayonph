<template>
    <table style="width: 100%" class="mt-2">
        <tr>
            <td>ORDER ID</td>
            <td align="right" class="font-weight-bold primary--text">
                {{ orderId.toString().padStart(5, '0') }}
            </td>
        </tr>
        <tr>
            <td>ORDERED AT</td>
            <td align="right" class="primary--text">
                {{ dateTime }}
            </td>
        </tr>
        <template v-if="orderStatus != null">
            <tr>
                <td>STATUS</td>
                <td align="right" class="font-weight-bold primary--text">
                    {{ orderStatus.status }}
                </td>
            </tr>
            <tr v-if="orderStatus.date_time != null">
                <td>DATE & TIME</td>
                <td align="right" class="primary--text">
                    {{ orderStatus.date_time }}
                </td>
            </tr>
            <tr v-if="orderStatus.remarks != null">
                <td colspan="2">
                    REMARKS
                    <p class="primary--text">{{ orderStatus.remarks }}</p>
                </td>
            </tr>
        </template>
    </table>
</template>

<script>
    export default {
        name: 'OrderOverview',
        components: {},
        props: {
            orderId: {
                type   : Number,
                default: 0
            },
            dateTime: {
                type   : String,
                default: ''
            },
            orderStatus: {
                type   : Object,
                default: null
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
