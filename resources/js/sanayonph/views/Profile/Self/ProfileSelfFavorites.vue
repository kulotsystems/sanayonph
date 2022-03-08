<template>
    <v-app>
        <dialogs/>
        <toolbar-main></toolbar-main>
        <v-main>
            <v-container class="pa-2 pa-sm-3">
                <loader-soon/>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        name: 'ProfileSelfFavorites',
        components: {
            'dialogs'     : () => import('../../../components/dialogs/Dialogs.vue'),
            'toolbar-main': () => import('../../../components/toolbars/ToolbarMain.vue'),
            'loader-soon' : () => import('../../../components/loaders/LoaderSoon.vue'),
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
