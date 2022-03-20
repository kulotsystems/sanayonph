<template>
    <div>
        <v-form ref="accountForm">

            <!-- GCASH -->
            <v-row class="mt-3 mb-3" dense>
                <v-col cols="12">
                    <h4 class="font-weight-thin primary--text mb-0 mt-2">GCASH</h4>
                </v-col>

                <!-- Gcash Name -->
                <v-col cols="12" sm="6">
                    <v-text-field
                        label="Gcash Name"
                        v-model="request.gcash_name"
                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'gcash_name')]"
                        @keyup="$store.dispatch('form/reset', [response, 'gcash_name']).then(r => response)"
                        :disabled="config.btnSave.loading"
                        class="uppercase"
                        placeholder="ex: JUAN DELA CRUZ"
                    />
                </v-col>

                <!-- Gcash Number -->
                <v-col cols="12" sm="6">
                    <v-text-field
                        label="Gcash Number"
                        v-model="request.gcash_number"
                        :counter="config.gcash_number.chars"
                        :rules="[$store.state.form.rules.required, $store.state.form.rules.chars(request.gcash_number, config.gcash_number.chars, 'digits'), $store.state.form.rules.feedback(response, 'gcash_number')]"
                        @keyup="$store.dispatch('form/reset', [response, 'gcash_number']).then(r => response)"
                        :disabled="config.btnSave.loading"
                        class="uppercase"
                        placeholder="ex: 09123456789"
                    />
                </v-col>
            </v-row>

            <!-- STORE -->
            <v-row class="mt-3 mb-3" dense>
                <v-col cols="12">
                    <h4 class="font-weight-thin primary--text mb-0 mt-2">STORE</h4>
                </v-col>

                <!-- Store Name -->
                <v-col cols="12" sm="6">
                    <v-text-field
                        label="Store Name"
                        v-model="request.store_name"
                        :rules="[$store.state.form.rules.feedback(response,'store_name')]"
                        @keyup="$store.dispatch('form/reset', [response, 'store_name']).then(r => response)"
                        :disabled="config.btnSave.loading"
                    />
                </v-col>

                <!-- Store Description -->
                <v-col cols="12">
                    <v-textarea
                        label="Store Description"
                        v-model="request.store_description"
                        :counter="config.store_description.max"
                        :rules="[$store.state.form.rules.max_chars(request.store_description, config.store_description.max), $store.state.form.rules.feedback(response, 'store_description')]"
                        @keyup="$store.dispatch('form/reset', [response, 'store_description']).then(r => response)"
                        class="text-body-1 uppercase"
                        autocomplete="off"
                        auto-grow
                    />
                </v-col>
            </v-row>

        </v-form>

        <v-row class="mt-3" dense>
            <v-col cols="12" align="right">
                <button-action
                    label="SAVE"
                    icon="save"
                    color="primary"
                    rounded
                    large
                    :loading="config.btnSave.loading"
                    :disabled="!dirty"
                    @click="save"
                />
            </v-col>
        </v-row>
    </div>
</template>

<script>
    import api_user from '../../../api/api-user.js';

    export default {
        name: 'ProfileSelfEditAccount',
        components: {
            'button-action': () => import('../../../components/buttons/ButtonAction.vue')
        },
        data() {
            return {
                config: {
                    gcash_number: {
                        chars: 11
                    },
                    btnSave: {
                        loading: false
                    },
                    store_description: {
                        max: 300
                    }
                },
                request: {
                    gcash_name       : this.$store.getters['auth/user'].gcash_name,
                    gcash_number     : this.$store.getters['auth/user'].gcash_number,
                    store_name       : this.$store.getters['auth/user'].store.name,
                    store_description: this.$store.getters['auth/user'].store.description
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
             * METHOD: SAVE
             * Save account info
             */
            save() {
                let accountForm = this.$refs.accountForm;
                if(!this.config.btnSave.loading && accountForm.validate()) {
                    this.config.btnSave.loading = true;
                    api_user.updateAccount(this.request).then(response => {
                        if(!response) return;

                        if(response.data.user != null) {
                            this.$store.commit('auth/setUser', response.data.user);
                            this.cache = JSON.stringify(this.request);
                            this.cacheCTR += 1; // force cache
                        }
                        this.config.btnSave.loading = false;
                    }).catch(errors => {
                        this.config.btnSave.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                        accountForm.validate();
                    });
                }
            }
        },

        created() {
            this.cache = JSON.stringify(this.request);
        }
    }
</script>

<style scoped>

</style>
