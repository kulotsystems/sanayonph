<template>
    <v-app>
        <toolbar-main></toolbar-main>
        <v-main class="yellow">
            <v-container class="pa-2 pa-sm-3">
                <h1>NOT FOUND!</h1>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        name: 'Error404NotFound',
        components: {
            'toolbar-main': () => import('../components/toolbars/ToolbarMain.vue'),
        },
        data() {
            return {}
        },
        computed: {},
        methods: {}
    }
</script>

<style scoped>

</style>
