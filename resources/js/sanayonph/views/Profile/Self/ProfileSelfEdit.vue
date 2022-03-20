<template>
    <v-app>
        <dialogs/>
        <toolbar-main>
            <tab-profile-edit slot="tabs"/>
        </toolbar-main>
        <v-main class="secondary">
            <bg-secondary/>
            <v-container class="pa-2 pa-sm-3">
                <v-row class="justify-center">
                    <v-col cols="12" sm="12" md="8">
                        <v-tabs-items v-model="$store.state['tab/profile-edit']" class="transparent" @change="tabChange($event)">
                            <v-tab-item id="personal">
                                <v-card>
                                    <v-toolbar class="grey lighten-5" dense flat>
                                        <span class="v-toolbar-text text-body font-weight-light grey--text">PERSONAL INFO</span>
                                    </v-toolbar>
                                    <v-card-text>
                                        <profile-self-edit-personal/>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                            <v-tab-item id="photo">
                                <v-card>
                                    <v-toolbar class="grey lighten-5" dense flat>
                                        <span class="v-toolbar-text text-body font-weight-light grey--text">PHOTO</span>
                                    </v-toolbar>
                                    <v-card-text>
                                        <profile-self-edit-photo/>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                            <v-tab-item id="account">
                                <v-card>
                                    <v-toolbar class="grey lighten-5" dense flat>
                                        <span class="v-toolbar-text text-body font-weight-light grey--text">ACCOUNT INFO</span>
                                    </v-toolbar>
                                    <v-card-text>
                                        <profile-self-edit-account/>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                        </v-tabs-items>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        name: 'MenusProfile',
        components: {
            'bg-secondary'   : () => import('../../../components/backgrounds/BackgroundSecondary.vue'),
            'dialogs'         : () => import('../../../components/dialogs/Dialogs.vue'),
            'toolbar-main'    : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'tab-profile-edit': () => import('../../../components/toolbars/tabs/TabProfileEdit.vue'),
            'profile-self-edit-personal': () => import('./ProfileSelfEditPersonal.vue'),
            'profile-self-edit-photo'   : () => import('./ProfileSelfEditPhoto.vue'),
            'profile-self-edit-account' : () => import('./ProfileSelfEditAccount.vue'),
        },
        data() {
            return {}
        },
        computed: {},
        methods: {

            /****************************************************************************************************
             * METHOD: TAB CHANGE
             * Handle v-tabs-item change
             */
            tabChange(tabID) {
                this.$router.push(tabID);
                this.$store.commit('tab/pushHistory', {
                    routeName: this.$route.name,
                    tabID    : tabID,
                    tabKey   : window.history.state.key
                });
            },
        }
    }
</script>

<style scoped>

</style>
