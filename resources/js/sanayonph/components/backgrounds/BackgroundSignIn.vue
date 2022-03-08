<template>
    <v-img :src="background" style="position: fixed; width: 100%; height: 100%" eager/>
</template>

<script>
    export default {
        name: 'BackgroundSignIn',
        components: {},
        data() {
            return {}
        },
        computed: {
            background() {
                return this.$vuetify.breakpoint.xs ? this.$store.getters['path/signInBGMobile'] : this.$store.getters['path/signInBGDesktop'];
            }
        },
        methods : {}
    }
</script>

<style scoped>

</style>
