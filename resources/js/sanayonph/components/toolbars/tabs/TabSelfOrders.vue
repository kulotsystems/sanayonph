<template>
    <v-tabs v-model="$store.state['tab/' + $route.name]" align-with-title>
        <v-tab v-for="tab in tabs" :key="tab.id" :to="tab.id" @click="pushHistory(tab.id)">
            <span>{{ tab.label }}</span>
        </v-tab>
    </v-tabs>
</template>

<script>
    export default {
        name: 'TabSelfOrders',
        components: {},
        data() {
            return {
                tabs: [
                    {
                        id   : 'payment',
                        label: 'TO PAY'
                    },
                    {
                        id   : 'confirm',
                        label: 'TO CONFIRM'
                    },
                    {
                        id   : 'meetup',
                        label: 'FOR MEETUP'
                    },
                    {
                        id   : 'delivery',
                        label: 'FOR DELIVERY'
                    },
                    {
                        id   : 'completed',
                        label: 'COMPLETED'
                    },
                    {
                        id   : 'cancelled',
                        label: 'CANCELLED'
                    }
                ],
            }
        },
        computed: {},
        methods : {
            pushHistory(tabID) {
                if(this.$route.name !== tabID) {
                    this.$store.commit('tab/pushHistory', {
                        routeName: this.$route.name,
                        tabID    : tabID,
                        tabKey   : window.history.state.key
                    });
                }
            }
        },
        created() {
            // clear tab history
            this.$store.commit('tab/clearHistory', this.$route.name);

            // when back or forward browser button is clicked
            window.onpopstate = () => {
                this.$store.commit('tab/popHistory', {
                    routeName: this.$route.name,
                    tabID    : this.$route.params.tab,
                    tabKey   : window.history.state.key
                });
            };
        },
        beforeDestroy() {
            window.onpopstate = () => {}
        }
    }
</script>

<style scoped>

</style>
