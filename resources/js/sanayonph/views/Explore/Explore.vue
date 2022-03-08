<template>
    <v-app>
        <dialogs/>
        <toolbar-main @onBack="$emit('onBack', $event)"></toolbar-main>
        <v-main>
            <div class="with-bg" :style="{'background-image': $vuetify.breakpoint.xs ? `url(${$store.getters['path/yellowBGMobile']})` : `url(${$store.getters['path/yellowBGDesktop']})`}"/>
            <v-container>
            </v-container>
        </v-main>
        <nav-bottom></nav-bottom>
    </v-app>
</template>

<script>
    export default {
        name: 'Explore',
        components: {
            'dialogs'     : () => import('../../components/dialogs/Dialogs.vue'),
            'toolbar-main': () => import('../../components/toolbars/ToolbarMain.vue'),
            'nav-bottom'  : () => import('../../components/navs/NavBottom.vue'),
            'loader-soon' : () => import('../../components/loaders/LoaderSoon.vue'),
        },
        data() {
            return {
                query: ''
            }
        },
        computed: {},
        methods: {
            search(){
            }
        }
    }
</script>

<style scoped>

</style>
