<template>
    <v-app>
        <dialogs/>
        <toolbar-main>
            <!-- ADD DELIVERY ADDRESS BUTTON -->
            <div slot="fab">
                <button-floating icon="add" exact router :to="{ name: 'profile-delivery-addresses-add' }" right bottom/>
            </div>
        </toolbar-main>
        <v-main class="secondary">
            <bg-secondary/>
            <v-container class="pa-2 pa-sm-3">
                <!-- DELIVERY ADDRESSES -->
                <transition-group name="list-complete" tag="v-row">
                    <v-col cols="12" sm="6" md="4" v-for="(address, i) in config.addresses" :key="address.id" :class="{'pb-0': i < (config.addresses.length - 1) && $vuetify.breakpoint.xs, 'list-complete-item': config.animation}">
                        <v-card>
                            <v-toolbar class="grey lighten-5" dense flat>
                                <v-toolbar-title>
                                    <v-icon class="grey--text text--lighten-1">{{ $store.getters['icon/state'].address }}</v-icon>
                                </v-toolbar-title>
                            </v-toolbar>

                            <v-divider></v-divider>

                            <v-card-title class="text-body-1 d-block">
                                <p class="mb-0 one-line">
                                    <span class="grey--text">{{ i + 1 }}. </span>
                                    <span class="primary--text">{{ address.street }}, {{ address.barangay.name }}</span>
                                </p>
                                <p class="mb-0 text-body-2 one-line">
                                    <span class="primary--text">{{ address.barangay.muncity.name }}</span>, {{ address.barangay.muncity.province.name }}
                                    ({{ address.barangay.muncity.province.region.name }})
                                </p>
                            </v-card-title>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn fab class="primary lighten-1" depressed x-small exact router :to="{ name: 'profile-delivery-addresses-edit', params: { delivery_address: address.id } }" @click="config.animation=false">
                                    <v-icon>edit</v-icon>
                                </v-btn>
                                <v-btn fab class="primary lighten-1" depressed x-small @click="confirmDelete(i)">
                                    <v-icon>delete</v-icon>
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </transition-group>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    import api_delivery_addr from '../../../api/api-delivery-addr.js';

    export default {
        name: 'ProfileSelfDeliveryAddresses',
        components: {
            'bg-secondary'   : () => import('../../../components/backgrounds/BackgroundSecondary.vue'),
            'dialogs'        : () => import('../../../components/dialogs/Dialogs.vue'),
            'toolbar-main'   : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'button-floating': () => import('../../../components/buttons/ButtonFloating.vue'),
        },
        data() {
            return {
                config: {
                    fab: {
                        hidden: true
                    },
                    addresses : [],
                    animation : false
                }
            }
        },
        computed: {},
        methods: {

            /****************************************************************************************************
             * METHOD: CONFIRM DELETE
             * Confirm to delete selected delivery address
             */
            confirmDelete(i) {
                let address = this.config.addresses[i];
                this.$store.commit('dialog/confirm/show', {
                    title   : 'Delete Delivery Address',
                    prompt  : 'Do you really want to delete the following delivery address?<div class="mt-5"><small class="text-body-1 primary--text">' + address.address + '</small></div>',
                    noIcon  : 'arrow_back',
                    noLabel : 'BACK',
                    yesIcon : 'delete',
                    yesLabel: 'DELETE',
                    yesClass: 'error',
                    yesCallback: {
                        async : true,
                        action: () => {
                            api_delivery_addr.delete(address.id).then(response => {
                                if(!response) return;

                                if(response.data.deleted) {
                                    this.config.addresses.splice(i, 1);
                                }
                                this.$store.commit('dialog/confirm/hide');
                            }).catch(errors => {
                                this.$store.commit('dialog/confirm/errors', errors);
                            });
                        }
                    }
                });
            }
        },
        created() {
            setTimeout(() => {
                this.$store.commit('dialog/loader/show');
                api_delivery_addr.index().then(response => {
                    this.$store.commit('dialog/loader/hide');
                    if(!response) return;

                    this.config.addresses = response.data.addresses;
                    // enable animation after a delay
                    setTimeout(() => {
                        this.config.animation = true;
                    }, 500);
                }).catch(errors => {
                    this.$store.commit('dialog/loader/hide');
                    this.$store.commit('dialog/error/show', errors);
                });
            }, 350);
        }
    }
</script>

<style scoped>
    .one-line {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
</style>
