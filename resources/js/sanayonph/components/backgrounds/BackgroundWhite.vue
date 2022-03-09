<template>
    <v-img :src="background" style="position: fixed; width: 100%; height: 100%; opacity: 0.1" eager/>
</template>

<script>
    export default {
        name: 'BackgroundSecondary',
        components: {},
        data() {
            return {}
        },
        computed: {
            background() {
                return this.$vuetify.breakpoint.xs ? this.$store.getters['path/whiteBGMobile'] : this.$store.getters['path/whiteBGDesktop'];
            }
        },
        methods : {}
    }
</script>

<style scoped>

</style>
