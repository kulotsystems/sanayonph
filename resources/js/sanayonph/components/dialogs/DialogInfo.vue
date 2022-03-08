<template>
    <v-dialog v-model="config.isOpen" persistent max-width="400" hide-overlay no-click-animation>
        <v-card>
            <v-card-text align="center" class="py-16">
                <slot></slot>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        name: 'DialogInfo',
        components: {},
        data() {
            return {
                config: {
                    isOpen: true
                }
            }
        },
        computed: {},
        methods: {}
    }
</script>

<style scoped>

</style>
