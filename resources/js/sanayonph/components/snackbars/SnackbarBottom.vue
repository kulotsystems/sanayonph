<template>
    <v-snackbar v-model="isOpen" color="secondary"  bottom vertical timeout="3000">
        <span class="text-button">{{ snackbar.prompt }}</span>

        <template v-slot:action="{ attrs }">
            <v-btn
                color="yellow"
                text
                v-bind="attrs"
                @click="$store.commit('snackbar/bottom/hide')"
            >
                Close
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script>
    export default {
        name: 'SnackbarBottom',
        components: {},
        data() {
            return {}
        },
        computed: {
            snackbar() {
                return this.$store.getters['snackbar/bottom/state'];
            },
            isOpen: {
                get() {
                    return this.$store.getters['snackbar/bottom/state'].opened;
                },
                set() {
                    this.$store.commit('snackbar/bottom/hide');
                }
            }
        },
        methods: {},
        mounted() {
            this.$store.commit('snackbar/bottom/hide');
        }
    }
</script>

<style scoped>

</style>
