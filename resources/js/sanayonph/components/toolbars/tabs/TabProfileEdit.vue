<template>
    <v-tabs v-model="$store.state['tab/profile-edit']" align-with-title>
        <v-tab v-for="tab in tabs" :key="tab.id" :to="tab.id" @click="pushHistory(tab.id)">
            <span>{{ tab.label }}</span>
        </v-tab>
    </v-tabs>
</template>

<script>
    export default {
        name: 'TabProfileEdit',
        components: {},
        data() {
            return {
                tabs: [
                    {
                        id   : 'personal',
                        label: 'PERSONAL'
                    },
                    {
                        id   : 'photo',
                        label: 'PHOTO'
                    },
                    {
                        id   : 'account',
                        label: 'ACCOUNT'
                    },
                ]
            }
        },
        computed: {},
        methods : {
            pushHistory(tabID) {
                if(this.$route.name !== tabID) {
                    this.$store.commit('tab/pushHistory', {
                        routeName: 'profile-edit',
                        tabID    : tabID,
                        tabKey   : window.history.state.key
                    });
                }
            }
        },
        created() {
            // clear tab history
            this.$store.commit('tab/clearHistory', 'profile-edit');

            // when back or forward browser button is clicked
            window.onpopstate = () => {
                this.$store.commit('tab/popHistory', {
                    routeName: 'profile-edit',
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
