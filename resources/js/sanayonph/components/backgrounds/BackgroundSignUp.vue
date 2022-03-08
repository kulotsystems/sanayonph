<template>
    <v-img :src="background" style="position: fixed; width: 100%; height: 100%" eager/>
</template>

<script>
    export default {
        name: 'BackgroundSignUp',
        components: {},
        data() {
            return {}
        },
        computed: {
            background() {
                return this.$vuetify.breakpoint.xs ? this.$store.getters['path/signUpBGMobile'] : this.$store.getters['path/signUpBGDesktop'];
            }
        },
        methods : {}
    }
</script>

<style scoped>

</style>
