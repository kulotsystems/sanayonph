<template>
    <v-app>
        <dialogs/>
        <toolbar-main>
            <tab-store-orders slot="tabs"/>
        </toolbar-main>
        <v-main class="secondary">
            <bg-secondary/>
            <v-container class="pa-2 pa-sm-3">
                <v-tabs-items v-model="$store.state['tab/' + $route.name]" class="transparent" @change="changeTab($event)">
                    <!-- TO CONFIRM -->
                    <v-tab-item id="confirm">
                        <profile-self-store-orders-1-confirm/>
                    </v-tab-item>


                    <!-- FOR MEETUP -->
                    <v-tab-item id="meetup">
                        <profile-self-store-orders-2-meetup/>
                    </v-tab-item>


                    <!-- FOR DELIVERY -->
                    <v-tab-item id="delivery">
                        <profile-self-store-orders-3-delivery/>
                    </v-tab-item>


                    <!-- COMPLETED -->
                    <v-tab-item id="completed">
                        <profile-self-store-orders-4-completed/>
                    </v-tab-item>


                    <!-- CANCELLED -->
                    <v-tab-item id="cancelled">
                        <profile-self-store-orders-5-cancelled/>
                    </v-tab-item>
                </v-tabs-items>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        name: 'ProfileSelfStoreOrders',
        components: {
            'bg-secondary'   : () => import('../../../components/backgrounds/BackgroundSecondary.vue'),
            'dialogs'         : () => import('../../../components/dialogs/Dialogs.vue'),
            'toolbar-main'    : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'tab-store-orders': () => import('../../../components/toolbars/tabs/TabStoreOrders.vue'),
            'profile-self-store-orders-1-confirm'  : () => import('./ProfileSelfStoreOrders-1-Confirm.vue'),
            'profile-self-store-orders-2-meetup'   : () => import('./ProfileSelfStoreOrders-2-Meetup.vue'),
            'profile-self-store-orders-3-delivery' : () => import('./ProfileSelfStoreOrders-3-Delivery.vue'),
            'profile-self-store-orders-4-completed': () => import('./ProfileSelfStoreOrders-4-Completed.vue'),
            'profile-self-store-orders-5-cancelled': () => import('./ProfileSelfStoreOrders-5-Cancelled.vue')
        },
        data() {
            return {}
        },
        computed: {},
        methods : {
            /****************************************************************************************************
             * METHOD: CHANGE TAB
             * Handle v-tabs-item change
             */
            changeTab(tabID) {
                if(this.$route.params.tab !== tabID) {
                    this.$router.push(tabID);
                    this.$store.commit('tab/pushHistory', {
                        routeName: this.$route.name,
                        tabID    : tabID,
                        tabKey   : window.history.state.key
                    });
                }
            },
        }
    }
</script>

<style scoped>

</style>
