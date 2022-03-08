<template>
    <table style="width: 100%" class="mt-2">
        <tr class="text-body-1">
            <td><b>ORDER TOTAL</b></td>
            <td align="right" class="primary--text text-h6">
                <big><b><span class="font-weight-thin">&#8369;</span> {{ Number(total).toLocaleString() }}</b></big>
            </td>
        </tr>
    </table>
</template>

<script>
    export default {
        name: 'OrderTotal',
        components: {},
        props: {
            total: {
                type   : Number,
                default: 0
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
