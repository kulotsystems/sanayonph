<template>
    <nav>
        <v-app-bar class="white" app>

            <!-- BACK BUTTON -->
            <v-btn :icon="!hasCustomBack" :text="hasCustomBack" v-if="$route.meta.back || customBackRoute != null" @click="goBack">
                <v-icon :left="hasCustomBack">arrow_back</v-icon>
                <span v-if="hasCustomBack">{{ customBack }}</span>
            </v-btn>

            <!-- TITLE -->
            <v-app-bar-title v-if="$route.meta.title && !hideTitle" class="text-button primary--text pl-0">
                {{ customTitle === '' ? $route.meta.title : customTitle }}
            </v-app-bar-title>


            <v-spacer></v-spacer>


            <!-- ACTION BUTTON -->
            <slot name="action"></slot>


            <!-- HOME BUTTON -->
            <v-btn icon color="primary" exact router :to="{ name: 'shop' }" v-if="($route.meta.back || customBackRoute != null) && !$slots.fab && !$slots.action && $route.name !== 'sign-up' && $route.name !== 'reset-password' && $route.name !== 'shop'">
                <v-icon :left="hasCustomBack">home</v-icon>
            </v-btn>


            <!-- MENUS -->
            <slot name="menus"></slot>


            <!-- FAB -->
            <slot name="fab" v-slot:extension></slot>


            <!-- TABS -->
            <template v-slot:extension v-if="$store.state.tab[$route.name] != null && $route.params.tab != null">
                <slot name="tabs"></slot>
            </template>

        </v-app-bar>
    </nav>
</template>

<script>

    export default {
        name: 'ToolbarMain',
        components: {},
        props: {
            back: {
                type: Object
            },
            hideTitle: {
                type   : Boolean,
                default: false
            },
            customTitle: {
                type   : String,
                default: ''
            },
            customBack: {
                type   : String,
                default: ''
            },
            customBackRoute: {
                type   : Object,
                default: null
            }
        },
        data() {
            return {
                config: {
                    chat: true
                }
            }
        },
        methods : {
            goBack() {
                this.$store.commit('auth/skipIntro');
                this.$store.commit('history/goBack', {
                    navRight: {name: this.$route.name, params: this.$route?.params},
                    navLeft : this.customBackRoute != null ? this.customBackRoute : {name: this.$route.meta.back}
                });
            }
        },
        computed: {
            hasCustomBack() {
                return this.customBack !== '';
            }
        }
    }
</script>

<style scoped>
    .v-app-bar-title {
        text-transform: none !important;
        font-size: 1.2rem !important;
    }
</style>
