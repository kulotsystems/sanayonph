<template>
    <v-app>
        <dialogs/>
        <snackbar-bottom/>
        <toolbar-main>
            <button-action slot="action"      v-if="$route.name === config.addRoute"  label="SAVE"   icon="check" rounded text :loading="config.btnAdd.loading"    :disabled="!dirty" @click="storeCategory" />
            <button-action slot="action" v-else-if="$route.name === config.editRoute" label="UPDATE" icon="check" rounded text :loading="config.btnUpdate.loading" :disabled="!dirty" @click="updateCategory"/>
        </toolbar-main>
        <v-main class="secondary">
            <bg-secondary/>
            <v-container class="pa-2 pa-sm-3">
                <v-row class="justify-center">
                    <v-col cols="12" sm="12" md="8">
                        <v-form ref="categoryForm">
                            <v-card>
                                <v-toolbar class="grey lighten-5" dense flat>
                                    <v-icon class="grey--text text--lighten-1 mr-1" small>{{ $store.getters['icon/state'].category }}</v-icon>
                                    <span class="v-toolbar-text text-body font-weight-light grey--text">{{ config.origin }}</span>
                                </v-toolbar>

                                <v-divider></v-divider>
                                <v-card-text>
                                    <!-- CATEGORY NAME -->
                                    <v-row dense>
                                        <v-col cols="12">
                                            <v-text-field
                                                label="Category Name"
                                                v-model="request.category.name"
                                                :counter="config.name.max"
                                                :rules="[$store.state.form.rules.required, $store.state.form.rules.max_chars(request.name, config.name.max), $store.state.form.rules.feedback(response, 'category.name')]"
                                                @keyup="$store.dispatch('form/reset', [response, 'category.name']).then(r => response)"
                                                class="text-h6 uppercase"
                                                :loading="config.name.loading"
                                                :disabled="config.name.loading || config.btnAdd.loading || config.btnUpdate.loading"
                                                autocomplete="off"
                                            />
                                        </v-col>
                                    </v-row>

                                    <!-- CATEGORY DESCRIPTION -->
                                    <v-row dense>
                                        <v-col cols="12">
                                            <v-textarea
                                                label="Description"
                                                v-model="request.category.description"
                                                :counter="config.description.max"
                                                :rules="[$store.state.form.rules.max_chars(request.description, config.description.max), $store.state.form.rules.feedback(response, 'category.description')]"
                                                @keyup="$store.dispatch('form/reset', [response, 'category.description']).then(r => response)"
                                                class="text-body-1 uppercase"
                                                :loading="config.description.loading"
                                                :disabled="config.name.loading || config.btnAdd.loading || config.btnUpdate.loading"
                                                autocomplete="off"
                                                auto-grow
                                            />
                                        </v-col>
                                    </v-row>
                                </v-card-text>

                                <!-- RECORD BUTTONS -->
                                <v-card-actions>
                                    <record-actions
                                        :btn-add="config.btnAdd"
                                        :btn-update="config.btnUpdate"
                                        :add-route="config.addRoute"
                                        :edit-route="config.editRoute"
                                        :dirty="dirty"
                                        @add="storeCategory"
                                        @update="updateCategory"
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
    import api_category from '../../../api/api-category.js';

    export default {
        name: 'ProfileSelfStoreCategoriesModify',
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
                    addRoute : 'profile-store-categories-add',
                    editRoute: 'profile-store-categories-edit',
                    origin: '',
                    name  : {
                        loading: false,
                        max    : 50,
                    },
                    description: {
                        loading: false,
                        max    : 300
                    },
                    btnAdd: {
                        loading: false,
                    },
                    btnUpdate: {
                        loading: false,
                    }
                },
                request: {
                    category: {}
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
        methods: {
            /****************************************************************************************************
             * METHOD: FORCE CACHE
             * Force dirty() computed property to evaluate
             */
            forceCache() {
                this.cacheCTR += 1;
            },

            /****************************************************************************************************
             * METHOD: GO BACK
             * Navigate back to 'profile-store-categories' view
             */
            goBack() {
                this.$store.commit('history/goBack', {
                    navRight: {name: this.$route.name, params: this.$route?.params},
                    navLeft : {name: 'profile-store-categories'}
                });
            },


            /****************************************************************************************************
             * METHOD: ON LOAD
             * Get category details
             */
            onLoad() {
                if(this.$route.name === this.config.editRoute) {
                    this.config.name.loading = true;
                    api_category.edit(this.$route.params.category).then(response => {
                        if(!response) return;

                        this.request.category = response.data.category;
                        this.config.origin    = response.data.category.name;
                        this.config.name.loading = false;
                        this.cache = JSON.stringify(this.request);
                    }).catch(errors => {
                        this.config.name.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                    });
                }
            },


            /****************************************************************************************************
             * METHOD: STORE CATEGORY
             * Add inputted category
             */
            storeCategory() {
                let categoryForm = this.$refs.categoryForm;
                if(!this.config.btnAdd.loading && categoryForm.validate()) {
                    this.config.btnAdd.loading = true;
                    api_category.store(this.request.category).then(response => {
                        if(!response) return;

                        if(response.data.stored) {
                            this.config.btnAdd.loading = false;
                            this.goBack();
                        }
                    }).catch(errors => {
                        this.config.btnAdd.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                        categoryForm.validate();
                    });
                }
            },


            /****************************************************************************************************
             * METHOD: UPDATE CATEGORY
             * Update selected category
             */
            updateCategory() {
                let categoryForm = this.$refs.categoryForm;
                if(!this.config.btnUpdate.loading && categoryForm.validate()) {
                    this.config.btnUpdate.loading = true;
                    api_category.update(this.request.category, this.$route.params.category).then(response => {
                        if(!response) return;

                        if(response.data.updated) {
                            this.config.btnUpdate.loading = false;
                            this.$store.commit('snackbar/bottom/show', {
                                prompt: 'Category updated!'
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
                        categoryForm.validate();
                    });
                }
            },
        },
        mounted() {
            this.onLoad();
        }
    }
</script>

<style scoped>

</style>
