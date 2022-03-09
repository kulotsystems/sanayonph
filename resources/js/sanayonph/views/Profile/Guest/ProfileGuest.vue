<template>
    <v-app>
        <toolbar-main
            :custom-title="`@${$store.getters['store/user'].username}`"
            :custom-back-route="{ name: 'shop' }"
        />
        <v-main>

            <!-- PROFILE BANNER -->
            <div class="primary " style="height: 150px;">

            </div>

            <v-container class="pa-2 pa-sm-3">
                <v-row class="justify-center">
                    <v-col cols="12" sm="12" md="8">
                        <v-card style="border-radius: 0; background: transparent; margin-top: -111px;" flat>
                            <v-card-title class="white--text flex-nowrap">
                                <v-avatar size="111" exact route>
                                    <v-img
                                        :lazy-src="$store.getters['store/avatar'] !== '' ? $store.getters['path/userAvatar']['064'] + '/' + $store.getters['store/avatar'] : $store.getters['path/defaultAvatar']"
                                             :src="$store.getters['store/avatar'] !== '' ? $store.getters['path/userAvatar']['128'] + '/' + $store.getters['store/avatar'] : $store.getters['path/defaultAvatar']"
                                        aspect-ratio="1"
                                    />
                                </v-avatar>
                                <div class="ml-3">
                                    <template v-if="$vuetify.breakpoint.xs && $store.getters['store/user'].name.full_name_1.length > 19">
                                        <p class="ma-0 font-weight-thin" style="line-height: 1">
                                            {{ $store.getters['store/user'].first_name }}
                                        </p>
                                        <p class="ma-0 mt-1 font-weight-thin" style="line-height: 1">
                                            <template v-if="$store.getters['store/user'].middle_name">
                                                {{ $store.getters['store/user'].middle_name.substr(0, 1) }}.
                                            </template>
                                            {{ $store.getters['store/user'].last_name }}
                                            <template v-if="$store.getters['store/user'].name_suffix">
                                                {{ $store.getters['store/user'].name_suffix }}
                                            </template>
                                        </p>
                                        <p class="mt-2 pb-2 font-weight-thin" style="line-height: 1">
                                            <small class="text-spaced">@{{ $store.getters['store/user'].username }}</small>
                                        </p>
                                    </template>
                                    <template v-else>
                                        <p class="ma-0 font-weight-thin" style="line-height: 1">
                                            {{ $store.getters['store/user'].name.full_name_1 }}
                                        </p>
                                        <p class="ma-0 mt-1 mb-2 font-weight-thin" style="line-height: 1">
                                            <small class="text-spaced">@{{ $store.getters['store/user'].username }}</small>
                                        </p>
                                    </template>
                                </div>
                            </v-card-title>
                        </v-card>

                        <v-list>
                            <v-list-item-group v-model="config.menu.selectedItem" color="primary">
                                <v-list-item
                                    v-for="(item, i) in config.menu.items"
                                    :key="i"
                                    router
                                    :to="item.route"
                                    exact
                                >
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
        </v-main>
    </v-app>
</template>

<script>
    export default {
        name: 'ProfileGuest',
        components: {
            'toolbar-main' : () => import('../../../components/toolbars/ToolbarMain.vue')
        },
        data() {
            return {
                config: {
                    menu: {
                        selectedItem: 1,
                        items: [
                            { label: 'Store', icon: 'storefront', route: { name: 'profile-store', params: {store: 'store'} }},
                        ],
                    }
                }
            }
        },
        computed: {},
        methods: {}
    }
</script>

<style scoped>

</style>
