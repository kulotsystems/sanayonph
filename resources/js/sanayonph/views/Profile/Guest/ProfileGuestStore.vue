<template>
    <v-app>
        <toolbar-main
            :custom-title="`@${$store.getters['store/user'].username} Store`"
            :custom-back-route="{ name: 'shop' }"
        />
        <v-main class="yellow">
            <bg-yellow/>
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
        name: 'ProfileGuestStore',
        components: {
            'bg-yellow'   : () => import('../../../components/backgrounds/BackgroundYellow.vue'),
            'toolbar-main': () => import('../../../components/toolbars/ToolbarMain.vue'),
        },
        data() {
            return {
                config: {
                    pages:[
                        {
                            'title': 'Products',
                            'icon' : this.$store.state.icon.product,
                            'route': { name:'profile-store-products' }
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
