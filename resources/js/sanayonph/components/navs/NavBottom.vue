<template>
    <v-bottom-navigation active-class="grey lighten-4 primary--text" :height="$vuetify.breakpoint.mdAndUp ? 64 : 56" grow app>
        <v-btn v-for="navItem in config.navItems" :key="navItem.label" router :to="navItem.route" exact>
            <span>{{ navItem.label }}</span>
            <v-icon>{{ navItem.icon }}</v-icon>
        </v-btn>
    </v-bottom-navigation>
</template>

<script>
    export default {
        name: 'NavBottom',
        components: {},
        data() {
            return {
                config: {
                    navItems: [
                        {
                            label: 'Shop',
                            icon : 'home',
                            route: {
                                name: 'shop'
                            }
                        },
                        {
                            label: 'Explore',
                            icon : 'search',
                            route: {
                                name: 'explore'
                            }
                        },
                        {
                            label: 'Me',
                            icon : 'person',
                            route: {
                                name  : 'profile',
                                params: {
                                    username: this.$store.getters['auth/user'].username
                                }
                            }
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
