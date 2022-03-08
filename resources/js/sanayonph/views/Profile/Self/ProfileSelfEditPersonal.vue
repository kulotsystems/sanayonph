<template>
    <div>
        <v-form ref="personalForm">
            <v-row class="mt-3 mb-3" dense>
                <!-- First Name -->
                <v-col cols="12" sm="6" md="3">
                    <v-text-field
                        label="First Name"
                        v-model="request.first_name"
                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'first_name')]"
                        @keyup="$store.dispatch('form/reset', [response, 'first_name']).then(r => response)"
                        :disabled="config.btnSave.loading"
                        placeholder="ex: JUAN"
                    />
                </v-col>
                <!-- Middle Name -->
                <v-col cols="12" sm="6" md="3">
                    <v-text-field
                        label="Middle Name"
                        v-model="request.middle_name"
                        :rules="[$store.state.form.rules.feedback(response,'middle_name')]"
                        @keyup="$store.dispatch('form/reset', [response, 'middle_name']).then(r => response)"
                        :disabled="config.btnSave.loading"
                        placeholder="ex: REYES"
                        hint="(Optional)"
                    />
                </v-col>
                <!-- Last Name -->
                <v-col cols="12" sm="6" md="3">
                    <v-text-field
                        label="Last Name"
                        v-model="request.last_name"
                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'last_name')]"
                        @keyup="$store.dispatch('form/reset', [response, 'last_name']).then(r => response)"
                        :disabled="config.btnSave.loading"
                        placeholder="ex: DELA CRUZ"
                    />
                </v-col>
                <!-- Name Suffix -->
                <v-col cols="12" sm="6" md="3">
                    <v-text-field
                        label="Name Suffix"
                        v-model="request.name_suffix"
                        :rules="[$store.state.form.rules.feedback(response,'name_suffix')]"
                        @keyup="$store.dispatch('form/reset', [response, 'name_suffix']).then(r => response)"
                        :disabled="config.btnSave.loading"
                        placeholder="ex: JR."
                        hint="(Optional)"
                    />
                </v-col>
            </v-row>

            <v-row class="mt-3 mb-3" dense>
                <!-- Gender -->
                <v-col cols="12" sm="6">
                    <v-select
                        label="Gender"
                        v-model="request.gender"
                        :items="['Male', 'Female']"
                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'gender')]"
                        @change="$store.dispatch('form/reset', [response, 'gender']).then(r => response)"
                        :disabled="config.btnSave.loading"
                    />
                </v-col>
                <!-- Date of Birth -->
                <v-col cols="12" sm="6">
                    <v-dialog
                        ref="dob_dialog"
                        v-model="config.date_of_birth.modal"
                        :return-value.sync="request.date_of_birth"
                        persistent
                        width="290px"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                label="Date of Birth"
                                v-bind="attrs"
                                v-on="on"
                                :value="formattedDateOfBirth"
                                :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response,'date_of_birth')]"
                                @blur="$store.dispatch('form/reset', [response, 'date_of_birth']).then(r => response)"
                                :disabled="config.btnSave.loading"
                                readonly
                            />
                        </template>
                        <v-date-picker v-model="request.date_of_birth" scrollable>
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="config.date_of_birth.modal = false">CANCEL</v-btn>
                            <v-btn text color="primary" @click="$refs.dob_dialog.save(request.date_of_birth)">OK</v-btn>
                        </v-date-picker>
                    </v-dialog>
                </v-col>
            </v-row>
        </v-form>

        <v-row class="mt-3" dense>
            <v-col cols="12" align="right">
                <button-action
                    icon="check"
                    label="SAVE"
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
    import format   from 'date-fns/format';
    import api_user from '../../../api/api-user.js';

    export default {
        name: 'ProfileSelfEditPersonal',
        components: {
            'button-action': () => import('../../../components/buttons/ButtonAction.vue')
        },
        data() {
            return {
                config: {
                    date_of_birth: {
                        modal: false
                    },
                    btnSave: {
                        loading: false
                    }
                },
                request: {
                    first_name   : this.$store.getters['auth/user'].first_name,
                    middle_name  : this.$store.getters['auth/user'].middle_name,
                    last_name    : this.$store.getters['auth/user'].last_name,
                    name_suffix  : this.$store.getters['auth/user'].name_suffix,
                    gender       : this.$store.getters['auth/user'].gender === 1 ? 'Male' : 'Female',
                    date_of_birth: this.$store.getters['auth/user'].date_of_birth,
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
            },

            formattedDateOfBirth() {
                return this.request.date_of_birth ? format(new Date(this.request.date_of_birth), 'MMMM d, yyy') : '';
            }
        },
        methods: {
            /****************************************************************************************************
             * METHOD: SAVE
             * Save personal info
             */
            save() {
                let personalForm = this.$refs.personalForm;
                if(!this.config.btnSave.loading && personalForm.validate()) {
                    this.config.btnSave.loading = true;
                    api_user.updatePersonal(this.request).then(response => {
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
                        personalForm.validate();
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
