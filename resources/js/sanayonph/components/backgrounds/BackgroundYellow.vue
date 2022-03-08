<template>
    <v-img :src="background" style="position: fixed; width: 100%; height: 100%; opacity: 0.7" eager/>
</template>

<script>
    export default {
        name: 'BackgroundYellow',
        components: {},
        data() {
            return {}
        },
        computed: {
            background() {
                return this.$vuetify.breakpoint.xs ? this.$store.getters['path/yellowBGMobile'] : this.$store.getters['path/yellowBGDesktop'];
            }
        },
        methods : {}
    }
</script>

<style scoped>

</style>
