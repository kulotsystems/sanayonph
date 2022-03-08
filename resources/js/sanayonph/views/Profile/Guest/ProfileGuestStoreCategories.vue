<template>
    <v-app>
        <toolbar-main
            :custom-title="`@${$store.getters['store/user'].username} Categories`"
        />
        <v-main class="yellow">
            <bg-yellow/>
            <v-container class="pa-2 pa-sm-3">

            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        name: 'ProfileGuestStoreCategories',
        components: {
            'bg-yellow'   : () => import('../../../components/backgrounds/BackgroundYellow.vue'),
            'toolbar-main': () => import('../../../components/toolbars/ToolbarMain.vue')
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
