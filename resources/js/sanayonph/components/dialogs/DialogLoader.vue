<template>
    <v-dialog v-model="isOpen" max-width="80" content-class="loader text-center" :retain-focus="false" :overlay-opacity="0.1" persistent no-click-animation>
        <loader-circular/>
    </v-dialog>
</template>

<script>
    export default {
        name: 'DialogLoader',
        components: {
            'loader-circular': () => import('../../components/loaders/LoaderCircular.vue'),
        },
        data() {
            return {}
        },
        computed: {
            dialog() {
                return this.$store.getters['dialog/loader/state'];
            },
            isOpen: {
                get() {
                    return this.$store.getters['dialog/loader/state'].opened;
                },
                set() {
                    this.$store.commit('dialog/loader/hide');
                }
            }
        },
        methods: {}
    }
</script>

<style>
    .v-dialog.loader {
        overflow: hidden;
        transition: none;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
    }
</style>
