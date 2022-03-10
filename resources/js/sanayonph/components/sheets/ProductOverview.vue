<template>
    <table style="width: 100%" class="mb-3">
        <tr>
            <td style="width: 128px; vertical-align: top">
                <v-avatar size="128" rounded="0" class="rounded-t rounded-b">
                    <v-img v-if="image"
                           :lazy-src="$store.getters['path/defaultProduct']"
                           :src="`${$store.getters['path/productImg']['128']}/${image}`"
                           aspect-ratio="1"
                           @click="$store.commit('dialog/image/show', { title: label, image: `${$store.getters['path/productImg']['300']}/${image}` })"
                           style="cursor: pointer"
                    />
                    <v-img v-else
                           :src="$store.getters['path/defaultProduct']"
                           aspect-ratio="1"
                    />
                </v-avatar>
            </td>
            <td class="pa-3" style="vertical-align: top">
                <h3 class="font-weight-thin">{{ name }}</h3>
                <p class="mb-0 primary--text">
                    <template v-if="label != null">
                        {{ label }}
                        <template v-if="quantity != null && stock != null">
                            (x{{ quantity }})
                        </template>
                    </template>
                    <template v-else>&nbsp;</template>
                </p>
                <h3 class="mt-2 mb-0 primary--text" v-if="displayPrice">
                    <template v-if="price == null && stock ==null">&nbsp;</template>
                    <template v-else>
                        <big   v-if="price != null"><span class="font-weight-thin">&#8369;</span> {{ Number(price).toLocaleString() }}</big>
                        <small v-if="stock != null" class="ml-3 font-weight-thin">(Stock: {{ Number(stock).toLocaleString() }})</small>
                        <small v-if="quantity != null && stock == null" class="ml-3 font-weight-thin">(Quantity: {{ Number(quantity).toLocaleString() }})</small>
                    </template>
                </h3>
            </td>
        </tr>
    </table>
</template>

<script>
    export default {
        name: 'ProductOverview',
        components: {},
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
            displayPrice: {
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
