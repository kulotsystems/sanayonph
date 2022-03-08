<template>
    <table style="width: 100%" class="mt-2">
        <tr>
            <td>DELIVERY METHOD</td>
            <td v-if="deliveryMethod" align="right" class="primary--text">
                {{ deliveryMethod.name }}
            </td>
        </tr>
        <tr>
            <td>DELIVERY STATUS</td>
            <td align="right" class="primary--text">
                {{ deliveryStatus.status }}
            </td>
        </tr>
        <tr v-if="deliveryStatus.date_time != null">
            <td>DATE & TIME</td>
            <td align="right" class="primary--text">
                {{ deliveryStatus.date_time }}
            </td>
        </tr>
        <tr v-if="deliveryStatus.location != null">
            <td>LOCATION</td>
            <td align="right" class="primary--text">
                {{ deliveryStatus.location }}
            </td>
        </tr>
        <tr v-if="deliveryStatus.full_name != null">
            <td>FULL NAME</td>
            <td align="right" class="primary--text">
                {{ deliveryStatus.full_name }}
            </td>
        </tr>
        <tr v-if="deliveryStatus.mobile != null">
            <td>MOBILE NO.</td>
            <td align="right" class="primary--text">
                {{ deliveryStatus.mobile }}
            </td>
        </tr>
        <tr v-if="deliveryStatus.email != null">
            <td>E-MAIL</td>
            <td align="right" class="primary--text">
                {{ deliveryStatus.email }}
            </td>
        </tr>
        <tr v-if="deliveryStatus.courier != null">
            <td>COURIER</td>
            <td align="right" class="primary--text">
                {{ deliveryStatus.courier }}
            </td>
        </tr>
        <tr v-if="deliveryStatus.tracking_code != null">
            <td>TRACKING CODE</td>
            <td align="right" class="primary--text">
                {{ deliveryStatus.tracking_code }}
            </td>
        </tr>
        <tr v-if="deliveryStatus.remarks != null">
            <td colspan="2">
                REMARKS
                <p class="primary--text">{{ deliveryStatus.remarks }}</p>
            </td>
        </tr>
    </table>
</template>

<script>
    export default {
        name: 'DeliveryOverview',
        components: {},
        props: {
            deliveryMethod: {
                type   : Object,
                default: null
            },
            deliveryStatus: {
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
