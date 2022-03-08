<template>
    <v-app>
        <dialogs/>
        <toolbar-main>
            <tab-self-orders slot="tabs"/>
        </toolbar-main>
        <v-main class="yellow">
            <bg-yellow/>
            <v-container class="pa-2 pa-sm-3">
                <v-tabs-items v-model="$store.state['tab/' + $route.name]" class="transparent" @change="changeTab($event)">
                    <!-- TO PAY -->
                    <v-tab-item id="payment">
                        <profile-self-orders-1-payment/>
                    </v-tab-item>


                    <!-- TO CONFIRM -->
                    <v-tab-item id="confirm">
                        <profile-self-orders-2-confirm/>
                    </v-tab-item>


                    <!-- FOR MEETUP -->
                    <v-tab-item id="meetup">
                       <profile-self-orders-3-meetup/>
                    </v-tab-item>


                    <!-- FOR DELIVERY -->
                    <v-tab-item id="delivery">
                        <profile-self-orders-4-delivery/>
                    </v-tab-item>


                    <!-- COMPLETED -->
                    <v-tab-item id="completed">
                        <profile-self-orders-5-completed/>
                    </v-tab-item>


                    <!-- CANCELLED -->
                    <v-tab-item id="cancelled">
                        <profile-self-orders-6-cancelled/>
                    </v-tab-item>

                </v-tabs-items>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        name: 'ProfileSelfOrders',
        components: {
            'bg-yellow'      : () => import('../../../components/backgrounds/BackgroundYellow.vue'),
            'dialogs'        : () => import('../../../components/dialogs/Dialogs.vue'),
            'toolbar-main'   : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'tab-self-orders': () => import('../../../components/toolbars/tabs/TabSelfOrders.vue'),
            'profile-self-orders-1-payment'  : () => import('./ProfileSelfOrders-1-Payment.vue'),
            'profile-self-orders-2-confirm'  : () => import('./ProfileSelfOrders-2-Confirm.vue'),
            'profile-self-orders-3-meetup'   : () => import('./ProfileSelfOrders-3-Meetup.vue'),
            'profile-self-orders-4-delivery' : () => import('./ProfileSelfOrders-4-Delivery.vue'),
            'profile-self-orders-5-completed': () => import('./ProfileSelfOrders-5-Completed.vue'),
            'profile-self-orders-6-cancelled': () => import('./ProfileSelfOrders-6-Cancelled.vue')
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
