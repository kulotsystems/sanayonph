<template>
    <v-app>
        <toolbar-main>
            <!-- ADD CATEGORY BUTTON -->
            <div slot="fab">
                <button-floating icon="add" exact router :to="{ name: 'profile-store-categories-add' }" right bottom/>
            </div>
        </toolbar-main>
        <v-main class="secondary">
            <bg-secondary/>
            <v-container class="pa-2 pa-sm-3">
                <!-- CATEGORIES -->
                <transition-group name="list-complete" tag="v-row">
                    <v-col cols="12" sm="6" md="4" v-for="(category, i) in categories" :key="category.id" :class="{'pb-0': i < (categories.length - 1) && $vuetify.breakpoint.xs, 'list-complete-item': config.animation}">
                        <v-card>
                            <v-toolbar class="grey lighten-5" dense flat>
                                <v-toolbar-title>
                                    <v-icon class="grey--text text--lighten-1">{{ $store.getters['icon/state'].category }}</v-icon>
                                </v-toolbar-title>
                            </v-toolbar>

                            <v-divider></v-divider>

                            <v-card-title class="text-h6 d-block">
                                <p class="name mb-0"><span class="grey--text">{{ i + 1 }}. </span> {{ category.name }}</p>
                            </v-card-title>
                            <v-card-subtitle class="text-body-1">
                                <p class="description mb-0">{{ category.description }}</p>
                            </v-card-subtitle>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn fab class="primary lighten-1" depressed x-small exact router :to="{ name: 'profile-store-categories-edit', params: { category: category.id } }" @click="config.animation=false">
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
    import api_category from '../../../api/api-category.js';

    export default {
        name: 'ProfileSelfStoreCategories',
        components: {
            'bg-secondary'   : () => import('../../../components/backgrounds/BackgroundSecondary.vue'),
            'toolbar-main'   : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'button-floating': () => import('../../../components/buttons/ButtonFloating.vue'),
        },
        data() {
            return {
                config: {
                    animation : false
                },
                request: {

                },
                response: {
                    message: '',
                    errors : {}
                },
                categoriesCacheCtr: 0
            }
        },
        computed: {
            // computed categories
            categories() {
                let ctr = this.categoriesCacheCtr;
                return this.$store.getters['auth/data/categories'];
            }
        },
        methods : {

            /****************************************************************************************************
             * METHOD: CONFIRM DELETE
             * Confirm to delete selected category
             */
            confirmDelete(i) {
                let category = this.categories[i];
                this.$store.commit('dialog/confirm/show', {
                    title   : 'Delete Category',
                    prompt  : 'Do you really want to delete the following category?<div class="mt-5"><small class="text-body-1 primary--text">' + category.name + '</small></div>',
                    noIcon  : 'arrow_back',
                    noLabel : 'BACK',
                    yesIcon : 'delete',
                    yesLabel: 'DELETE',
                    yesClass: 'error',
                    yesCallback: {
                        async : true,
                        action: () => {
                            api_category.delete(category.id).then(response => {
                                if(!response) return;

                                if(response.data.deleted) {
                                    this.categories.splice(i, 1);
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
            if(this.categories == null) {
                setTimeout(() => {
                    this.$store.commit('dialog/loader/show');
                    api_category.index().then(response => {
                        this.$store.commit('dialog/loader/hide');
                        if(!response) return;

                        // commit to auth/data/categories vuex store module
                        this.$store.commit('auth/data/fill', {
                            key : 'categories',
                            data: response.data.categories
                        });
                        this.categoriesCacheCtr += 1;

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
            else {
                // enable animation after a delay
                setTimeout(() => {
                    this.config.animation = true;
                }, 500);
            }
        }
    }
</script>

<style scoped>
    .name {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .description {
        height: 3rem;
        font-size: 0.99rem;
        line-height: 1.5rem;
        max-height: 3rem;
        overflow: hidden;
        display: block;
        -webkit-line-clamp: 2;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        text-overflow: ellipsis;
        white-space: normal;
    }
</style>
