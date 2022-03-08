<template>
    <v-app>
        <snackbar-bottom/>
        <toolbar-main>
            <div slot="menus">
                <menu-profile slot="btn3" @signedOut="$emit('signedOut')"></menu-profile>
            </div>
        </toolbar-main>

        <v-main>
            <!-- PROFILE BANNER -->
            <div class="primary" style="height: 150px;">

            </div>

            <v-container class="pa-2 pa-sm-3">
                <v-row class="justify-center">
                    <v-col cols="12" sm="12" md="8">
                        <v-card style="border-radius: 0; background: transparent; margin-top: -111px;" flat>
                            <v-card-title class="white--text flex-nowrap" style="cursor: pointer" @click="gotoProfileEdit">
                                <v-avatar size="111">
                                    <v-img
                                        :lazy-src="$store.getters['auth/avatar'] !== '' ? $store.getters['path/userAvatar']['064'] + '/' + $store.getters['auth/avatar'] : $store.getters['path/defaultAvatar']"
                                             :src="$store.getters['auth/avatar'] !== '' ? $store.getters['path/userAvatar']['128'] + '/' + $store.getters['auth/avatar'] : $store.getters['path/defaultAvatar']"
                                        aspect-ratio="1"
                                    />
                                </v-avatar>
                                <div class="ml-3">
                                    <template v-if="$vuetify.breakpoint.xs && $store.getters['auth/user'].name.full_name_1.length > 19">
                                        <p class="ma-0 font-weight-thin" style="line-height: 1">
                                            {{ $store.getters['auth/user'].first_name }}
                                        </p>
                                        <p class="ma-0 mt-1 font-weight-thin" style="line-height: 1">
                                            <template v-if="$store.getters['auth/user'].middle_name">
                                                {{ $store.getters['auth/user'].middle_name.substr(0, 1) }}.
                                            </template>
                                            {{ $store.getters['auth/user'].last_name }}
                                            <template v-if="$store.getters['auth/user'].name_suffix">
                                                {{ $store.getters['auth/user'].name_suffix }}
                                            </template>
                                        </p>
                                        <p class="mt-2 pb-2 font-weight-thin" style="line-height: 1">
                                            <small class="text-subtitle-2">@{{ $store.getters['auth/user'].username }}</small>
                                        </p>
                                    </template>
                                    <template v-else>
                                        <p class="ma-0 font-weight-thin" style="line-height: 1">
                                            {{ $store.getters['auth/user'].name.full_name_1 }}
                                        </p>
                                        <p class="ma-0 mt-1 mb-2 font-weight-thin" style="line-height: 1">
                                            <small class="text-subtitle-2">@{{ $store.getters['auth/user'].username }}</small>
                                        </p>
                                    </template>
                                </div>
                            </v-card-title>
                        </v-card>

                        <v-list>
                            <v-list-item-group v-model="config.menu.selectedItem" color="primary">
                                <v-list-item v-for="(item, index) in config.menu.items" :key="item.label" @click="activateMenuItem(index)">
                                    <v-list-item-icon>
                                        <v-icon v-text="item.icon" color="primary"></v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title v-text="item.label" class="text-button"></v-list-item-title>
                                    </v-list-item-content>
                                    <v-icon>
                                        chevron_right
                                    </v-icon>
                                </v-list-item>
                            </v-list-item-group>
                        </v-list>
                    </v-col>
                </v-row>
            </v-container>

            <v-bottom-sheet v-model="config.beAMerchant.isOpen" persistent inset scrollable>
                <v-card>
                    <v-card-title class="px-4">
                        <span class="primary--text">Be a Merchant</span>
                        <v-spacer></v-spacer>
                        <v-btn icon @click="config.beAMerchant.isOpen = false">
                            <v-icon>close</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-card-text class="px-4">
                        <v-text-field
                            @focus="$event.target.select()"
                            ref="merchantCode"
                            readonly
                            v-model="config.beAMerchant.code"
                            append-icon="content_copy"
                            @click:append="copyMerchantCode"
                        />
                    </v-card-text>
                </v-card>
            </v-bottom-sheet>
        </v-main>
        <nav-bottom></nav-bottom>
    </v-app>
</template>

<script>
    import api_store from '../../../api/api-store.js';

    export default {
        name: 'Profile',
        components: {
            'snackbar-bottom': () => import('../../../components/snackbars/SnackbarBottom.vue'),
            'toolbar-main'   : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'nav-bottom'     : () => import('../../../components/navs/NavBottom.vue'),
            'menu-profile'   : () => import('../../../components/toolbars/menus/MenuProfile.vue'),
        },
        data() {
            return {
                config: {
                    menu: {
                        selectedItem: null,
                        items: [
                            { label: 'My Store'          , icon: 'storefront'     , route: { name: 'profile-store'             , params: {store: 'store'} }},
                            { label: 'My Orders'         , icon: 'credit_score'   , route: { name: 'profile-orders'            , }},
                            { label: 'Delivery Addresses', icon: 'place'          , route: { name: 'profile-delivery-addresses', }},
                            // { label: 'Notifications'     , icon: 'notifications'  , route: { name: 'profile-notifications'     , }},
                            // { label: 'Favorites'         , icon: 'favorite_border', route: { name: 'profile-favorites'         , }},
                            // { label: 'Recently viewed'   , icon: 'schedule'       , route: { name: 'profile-recent-views'      , }},
                            // { label: 'FAQs'              , icon: 'help_outline'   , route: { name: 'profile-faqs'              , }},
                        ],
                    },
                    beAMerchant: {
                        isOpen: false,
                        code  : null
                    }
                },
                request: {

                },
                response: {
                    message: '',
                    errors : {}
                },
            }
        },
        computed: {},
        methods : {
            /****************************************************************************************************
             * METHOD: GO PROFILE EDIT
             * Go to profile edit
             */
            gotoProfileEdit() {
                this.$router.push({ name: 'profile-edit', params: { tab: 'personal' } });
            },

            /****************************************************************************************************
             * METHOD: ACTIVATE MENU ITEM
             * Handle menu item click
             */
            activateMenuItem(index) {
                if(index === 0) {
                    // store menu item
                    // if(this.$store.getters['auth/user'].store.verified_at != null)
                    if(true)
                        this.$router.push(this.config.menu.items[index].route);
                    else {
                        if(this.config.beAMerchant.code != null)
                            this.config.beAMerchant.isOpen = true;
                        else {
                            this.$store.commit('dialog/loader/show');
                            api_store.merchantCode().then(response => {
                                this.$store.commit('dialog/loader/hide');
                                if(!response) return;

                                // retrieve merchant code
                                this.config.beAMerchant.code = response.data.code;

                                this.config.beAMerchant.isOpen = true;
                            }).catch(errors => {
                                this.$store.commit('dialog/loader/hide');
                                this.$store.commit('dialog/error/show', errors);
                                this.response.message = errors.response.data.message;
                                this.response.errors  = errors.response.data.errors;
                            });
                        }
                    }
                }
                else {
                    // other items
                    this.$router.push(this.config.menu.items[index].route);
                }
            },

            /****************************************************************************************************
             * METHOD: COPY MERCHANT CODE
             * Copy merchant code to clipboard
             */
            copyMerchantCode() {
                this.$refs.merchantCode.focus();
                document.execCommand('copy');
                this.$store.commit('snackbar/bottom/show', {
                    prompt: 'Merchant code copied!'
                });
            }

        },
        created() {
            // if(this.$store.getters['auth/user'].store.verified_at == null) {
            //     this.config.menu.items[0].label = 'Be a Merchant';
            // }
        }
    }
</script>

<style scoped>

</style>
