<template>
    <v-fab-transition>
        <v-btn
            fab
            absolute
            v-show="shown"
            :color="color"
            :large="large"
            :top="top"
            :right="right"
            :bottom="bottom"
            :left="left"
            :exact="exact"
            :router="router"
            :to="to"
            @click="$emit('click')"
        >
            <v-icon v-if="icon !== ''">{{ icon }}</v-icon>
        </v-btn>
    </v-fab-transition>
</template>

<script>
    export default {
        name: 'ButtonFloating',
        components: {},
        props: {
            icon : {
                type   : String,
                default: ''
            },
            color: {
                type   : String,
                default: 'primary'
            },
            large: {
                type   : Boolean,
                default: true
            },
            top: {
                type   : Boolean,
                default: false
            },
            right: {
                type   : Boolean,
                default: false
            },
            bottom: {
                type   : Boolean,
                default: false
            },
            left: {
                type   : Boolean,
                default: false
            },
            exact: {
                type   : Boolean,
                default: false
            },
            router: {
                type   : Boolean,
                default: false
            },
            to: {
                type   : Object
            }
        },
        data() {
            return {
                shown: false
            }
        },
        computed: {},
        methods: {},
        mounted() {
            setTimeout(() => {
                this.shown = true;
            }, 350);
        }
    }
</script>

<style scoped>
    .v-btn--left {
        left: 0 !important;
    }
    .v-btn--right {
        right: 0 !important;
    }
</style>
