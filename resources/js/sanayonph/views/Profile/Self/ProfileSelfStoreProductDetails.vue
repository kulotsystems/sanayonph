<template>
    <v-app>

    </v-app>
</template>

<script>
    import api_product from '../../../api/api-product.js';

    export default {
        name: 'ProfileSelfStoreProductDetails',
        components: {},
        data() {
            return {
                product: null
            }
        },
        computed: {},
        methods : {},
        mounted() {

            // TEMPORARY:
            this.$router.replace({
                name: 'profile-store-products-edit',
                params: {
                    username: this.$route.params.username,
                    store   : 'store',
                    product : this.$route.params.product,
                    tab     : 'general'
                }
            });

            // api_product.show(this.$route.params.product).then(response => {
            //     if(!response) return;
            //
            //     this.product = response.data.product;
            // }).catch(errors => {
            //     this.$store.commit('dialog/loader/hide');
            //     this.$store.commit('dialog/error/show', errors);
            // });
        }
    }
</script>

<style scoped>

</style>
