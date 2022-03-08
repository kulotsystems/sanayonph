<template>
    <v-app>
        <dialogs/>
        <toolbar-main></toolbar-main>
        <v-main class="secondary">
            <bg-secondary/>
            <v-container class="pa-2 pa-sm-3">
                <transition-group name="list-complete" tag="v-row">
                    <v-col class="pb-0 list-complete-item" cols="12" sm="6" md="4" v-for="(page, index) in config.pages" :key="page.title">
                        <v-card hover exact router :to="page.route">
                            <v-card-text class="py-12" align="center">
                                <v-icon x-large color="primary">{{ page.icon }}</v-icon>
                                <h1 class="font-weight-thin mt-5 primary--text">{{ page.title }}</h1>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </transition-group>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        name: 'ProfileSelfStore',
        components: {
            'bg-secondary'   : () => import('../../../components/backgrounds/BackgroundSecondary.vue'),
            'dialogs'     : () => import('../../../components/dialogs/Dialogs.vue'),
            'toolbar-main': () => import('../../../components/toolbars/ToolbarMain.vue')
        },
        data() {
            return {
                config: {
                    pages:[
                        {
                            'title': 'Categories',
                            'icon' : this.$store.state.icon.category,
                            'route': { name:'profile-store-categories' }
                        },
                        {
                            'title': 'Products',
                            'icon' : this.$store.state.icon.product,
                            'route': { name:'profile-store-products' }
                        },
                        {
                            'title': 'Orders',
                            'icon' : this.$store.state.icon.order,
                            'route': { name:'profile-store-orders' }
                        }
                    ]
                }
            }
        },
        computed: {},
        methods : {}
    }
</script>

<style scoped>

</style>
