<template>
    <v-app>
        <dialogs/>
        <snackbar-bottom/>
        <toolbar-main>
            <button-action slot="action"      v-if="$route.name === config.addRoute"  label="SAVE"   icon="save" rounded outlined text :loading="config.btnAdd.loading"    :disabled="!dirty" @click="storeAddress" />
            <button-action slot="action" v-else-if="$route.name === config.editRoute" label="UPDATE" icon="save" rounded outlined text :loading="config.btnUpdate.loading" :disabled="!dirty" @click="updateAddress"/>
        </toolbar-main>
        <v-main class="secondary">
            <bg-secondary/>
            <v-container class="pa-2 pa-sm-3">
                <v-row class="justify-center">
                    <v-col cols="12" sm="12" md="8">
                        <v-form ref="addressForm">
                            <v-card>
                                <v-toolbar class="grey lighten-5" dense flat>
                                    <v-icon class="grey--text text--lighten-1 mr-1" small>{{ $store.getters['icon/state'].address }}</v-icon>
                                    <span class="v-toolbar-text text-body font-weight-light grey--text">{{ config.origin }}</span>
                                </v-toolbar>

                                <v-divider></v-divider>

                                <v-card-text>
                                    <!-- REGION -->
                                    <v-row dense>
                                        <v-col cols="12">
                                            <v-autocomplete
                                                label="Region"
                                                v-model="request.region"
                                                class="text-body-1 uppercase"
                                                :rules="[$store.state.form.rules.required]"
                                                :items="config.region.items"
                                                :item-text="item  => `${item.name}`"
                                                :item-value="item => `${item.id}`"
                                                :loading="config.region.loading"
                                                :disabled="config.region.disabled || config.btnAdd.loading || config.btnUpdate.loading"
                                                autocomplete="off"
                                                @change="onRegionChange"
                                            />
                                        </v-col>
                                    </v-row>

                                    <!-- PROVINCE -->
                                    <v-row dense>
                                        <v-col cols="12">
                                            <v-autocomplete
                                                label="Province"
                                                v-model="request.province"
                                                class="text-body-1 uppercase"
                                                :rules="[$store.state.form.rules.required]"
                                                :items="config.province.items"
                                                :item-text="item  => `${item.name}`"
                                                :item-value="item => `${item.id}`"
                                                :loading="config.province.loading"
                                                :disabled="request.region <= 0 || config.btnAdd.loading || config.btnUpdate.loading"
                                                autocomplete="off"
                                                @change="onProvinceChange"
                                            />
                                        </v-col>
                                    </v-row>

                                    <!-- MUNCITY -->
                                    <v-row dense>
                                        <v-col cols="12">
                                            <v-autocomplete
                                                label="Municipality / City"
                                                v-model="request.muncity"
                                                class="text-body-1 uppercase"
                                                :rules="[$store.state.form.rules.required]"
                                                :items="config.muncity.items"
                                                :item-text="item  => `${item.name}`"
                                                :item-value="item => `${item.id}`"
                                                :loading="config.muncity.loading"
                                                :disabled="request.province <= 0 || config.btnAdd.loading || config.btnUpdate.loading"
                                                autocomplete="off"
                                                @change="onMuncityChange"
                                            />
                                        </v-col>
                                    </v-row>

                                    <!-- BARANGAY -->
                                    <v-row dense>
                                        <v-col cols="12">
                                            <v-autocomplete
                                                label="Barangay"
                                                v-model="request.barangay"
                                                class="text-body-1 uppercase"
                                                :rules="[$store.state.form.rules.required]"
                                                :items="config.barangay.items"
                                                :item-text="item  => `${item.name}`"
                                                :item-value="item => `${item.id}`"
                                                :loading="config.barangay.loading"
                                                :disabled="request.muncity <= 0 || config.btnAdd.loading || config.btnUpdate.loading"
                                                autocomplete="off"
                                                @change="onBarangayChange"
                                            />
                                        </v-col>
                                    </v-row>

                                    <!-- STREET -->
                                    <v-row dense>
                                        <v-col cols="12">
                                            <v-text-field
                                                label="Zone / Street"
                                                v-model="request.street"
                                                :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response, 'street')]"
                                                @keyup="$store.dispatch('form/reset', [response, 'street']).then(r => response)"
                                                class="text-body-1 uppercase"
                                                :loading="config.street.loading"
                                                :disabled="request.barangay <= 0 || config.btnAdd.loading || config.btnUpdate.loading"
                                                autocomplete="off"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-card-text>

                                <!-- ACTION BUTTONS -->
                                <v-card-actions>
                                    <record-actions
                                        :btn-add="config.btnAdd"
                                        :btn-update="config.btnUpdate"
                                        :add-route="config.addRoute"
                                        :edit-route="config.editRoute"
                                        :dirty="dirty"
                                        @add="storeAddress"
                                        @update="updateAddress"
                                        @cancel="goBack"
                                    />
                                </v-card-actions>
                            </v-card>
                        </v-form>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    import api_delivery_addr from '../../../api/api-delivery-addr.js';
    import api_ph            from '../../../api/api-ph.js';

    export default {
        name: 'ProfileSelfDeliveryAddressesModify',
        components: {
            'bg-secondary'   : () => import('../../../components/backgrounds/BackgroundSecondary.vue'),
            'dialogs'        : () => import('../../../components/dialogs/Dialogs.vue'),
            'snackbar-bottom': () => import('../../../components/snackbars/SnackbarBottom.vue'),
            'toolbar-main'   : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'record-actions' : () => import('../../../components/buttons/RecordActions.vue'),
            'button-action'  : () => import('../../../components/buttons/ButtonAction.vue')
        },
        data() {
            return {
                config: {
                    addRoute : 'profile-delivery-addresses-add',
                    editRoute: 'profile-delivery-addresses-edit',
                    region: {
                        items   : [],
                        loading : false,
                        disabled: false
                    },
                    province: {
                        items   : [],
                        loading : false,
                    },
                    muncity: {
                        items   : [],
                        loading : false,
                    },
                    barangay: {
                        items   : [],
                        loading : false,
                    },
                    street: {
                        loading: false
                    },
                    btnAdd: {
                        loading : false,
                    },
                    btnUpdate: {
                        loading : false,
                    }
                },
                request: {
                    region  : 0,
                    province: 0,
                    muncity : 0,
                    barangay: 0,
                    street  : ''
                },
                response: {
                    message: '',
                    errors : {}
                },
                cacheCTR: 1
            }
        },
        computed: {
            dirty() {
                let temp = this.cacheCTR;
                return this.cache !== JSON.stringify(this.request);
            }
        },
        methods : {
            /****************************************************************************************************
             * METHOD: FORCE CACHE
             * Force dirty() computed property to evaluate
             */
            forceCache() {
                this.cacheCTR += 1;
            },

            /****************************************************************************************************
             * METHOD: GO BACK
             * Navigate back to 'profile-delivery-addresses' view
             */
            goBack() {
                this.$store.commit('history/goBack', {
                    navRight: {name: this.$route.name, params: this.$route?.params},
                    navLeft : {name: 'profile-delivery-addresses'}
                });
            },


            /****************************************************************************************************
             * METHOD: ON LOAD - GET REGIONS
             * Get all available regions or delivery address details
             */
            onLoad() {
                if(this.$route.name === this.config.addRoute) {
                    this.config.region.loading = true;
                    api_ph.regions().then(response => {
                        if(!response) return;

                        this.config.region.items   = response.data;
                        this.config.region.loading = false;
                    }).catch(errors => {
                        this.$store.commit('dialog/error/show', errors);
                    });
                }
                else if(this.$route.name === this.config.editRoute) {
                    this.config.region.disabled  = true;
                    this.config.region.loading   = true;
                    this.config.province.loading = true;
                    this.config.muncity.loading  = true;
                    this.config.barangay.loading = true;
                    this.config.street.loading   = true;

                    api_delivery_addr.edit(this.$route.params.delivery_address).then(response => {
                        if(!response) return;

                        if(response.data.origin != null) {
                            const origin = response.data.origin;

                            this.config.region.items   = origin.regions;
                            this.config.province.items = origin.provinces;
                            this.config.muncity.items  = origin.muncities;
                            this.config.barangay.items = origin.barangays;

                            this.request.region   = origin.region_id.toString();
                            this.request.province = origin.province_id.toString();
                            this.request.muncity  = origin.muncity_id.toString();
                            this.request.barangay = origin.barangay_id.toString();
                            this.request.street   = origin.street;

                            this.config.origin = response.data.address;
                            this.cache = JSON.stringify(this.request);
                        }
                        else
                            this.$router.replace({ name: 'profile-delivery-addresses' });

                        this.config.region.disabled  = false;
                        this.config.region.loading   = false;
                        this.config.province.loading = false;
                        this.config.muncity.loading  = false;
                        this.config.barangay.loading = false;
                        this.config.street.loading   = false;
                    }).catch(errors => {
                        this.$store.commit('dialog/error/show', errors);
                    });
                }
            },


            /****************************************************************************************************
             * METHOD: STORE ADDRESS
             * Add selected address
             */
            storeAddress() {
                let addressForm = this.$refs.addressForm;
                if(!this.config.btnAdd.loading && addressForm.validate()) {
                    this.config.btnAdd.loading = true;
                    api_delivery_addr.store({
                        barangay: this.request.barangay,
                        street  : this.request.street
                    }).then(response => {
                        if(!response) return;

                        if(response.data.stored) {
                            this.config.btnAdd.loading = false;
                            this.$store.commit('auth/data/purge', 'addresses');
                            this.goBack();
                        }
                    }).catch(errors => {
                        this.config.btnAdd.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                        addressForm.validate();
                    });
                }
            },


            /****************************************************************************************************
             * METHOD: UPDATE ADDRESS
             * Update selected address
             */
            updateAddress() {
                let addressForm = this.$refs.addressForm;
                if(!this.config.btnUpdate.loading && addressForm.validate()) {
                    this.config.btnUpdate.loading = true;
                    api_delivery_addr.update({
                        barangay: this.request.barangay,
                        street  : this.request.street
                    }, this.$route.params.delivery_address).then(response => {
                        if(!response) return;

                        if(response.data.updated) {
                            this.config.btnUpdate.loading = false;
                            this.$store.commit('auth/data/purge', 'addresses');
                            this.$store.commit('snackbar/bottom/show', {
                                prompt: 'Delivery address updated!'
                            });

                            // cache
                            this.cache = JSON.stringify(this.request);
                            this.forceCache();
                        }
                    }).catch(errors => {
                        this.config.btnUpdate.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                        addressForm.validate();
                    });
                }
            },


            /****************************************************************************************************
             * METHOD: ON REGION CHANGE - GET PROVINCES
             * Get available provinces in the selected region
             */
            onRegionChange() {
                this.request.province = 0;
                this.request.muncity  = 0;
                this.request.barangay = 0;
                if(this.request.region > 0) {
                    this.config.province.loading = true;
                    api_ph.provinces({
                        region_id: this.request.region
                    }).then(response => {
                        if(!response) return;

                        this.config.province.items = response.data;
                        this.config.province.loading = false;
                    }).catch(errors => {
                        this.$store.commit('dialog/error/show', errors);
                    });
                }
            },

            /****************************************************************************************************
             * METHOD: ON PROVINCE CHANGE - GET MUNCITIES
             * Get available municipalities / cities in the selected province
             */
            onProvinceChange() {
                this.request.muncity = 0;
                this.request.barangay = 0;
                if(this.request.province > 0) {
                    this.config.muncity.loading = true;
                    api_ph.muncities({
                        province_id: this.request.province
                    }).then(response => {
                        if(!response) return;

                        this.config.muncity.items   = response.data;
                        this.config.muncity.loading = false;
                    }).catch(errors => {
                        this.$store.commit('dialog/error/show', errors);
                    });
                }
            },

            /****************************************************************************************************
             * METHOD: ON MUNCITY CHANGE - GET BARANGAYS
             * Get available barangays in the selected municipality / city
             */
            onMuncityChange() {
                this.request.barangay = 0;
                if(this.request.muncity > 0) {
                    this.config.barangay.loading = true;
                    api_ph.barangays({
                        muncity_id: this.request.muncity
                    }).then(response => {
                        if(!response) return;

                        this.config.barangay.items = response.data;
                        this.config.barangay.loading  = false;
                    }).catch(errors => {
                        this.$store.commit('dialog/error/show', errors);
                    });
                }
            },

            /****************************************************************************************************
             * METHOD: ON BARANGAY CHANGE - PREPARE STREET
             *
             */
            onBarangayChange() {

            }

        },
        mounted() {
            this.onLoad();
        }
    }
</script>

<style scoped>

</style>
