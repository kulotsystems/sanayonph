<template>
    <v-dialog v-model="isOpen" :retain-focus="false" :max-width="maxWidth">
        <v-card tile elevation="24">
            <!-- IMAGE TITLE -->
            <v-card-title class="pa-3">
                <small class="primary--text">{{ dialog.title }}</small>
                <v-spacer></v-spacer>
                <v-btn @click="$store.commit('dialog/image/hide')" icon>
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>

            <!-- IMAGE -->
            <v-card-text class="pa-2 pt-0">
                <v-img :src="dialog.image" style="border-radius: 4px;">
                    <template v-slot:placeholder>
                        <v-row class="fill-height ma-0" align="center" justify="center">
                            <v-progress-circular indeterminate color="primary lighten-3"/>
                        </v-row>
                    </template>
                </v-img>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        name: 'DialogImage',
        components: {},
        data() {
            return {}
        },
        computed: {
            dialog() {
                return this.$store.getters['dialog/image/state'];
            },
            isOpen: {
                get() {
                    return this.$store.getters['dialog/image/state'].opened;
                },
                set() {
                    this.$store.commit('dialog/image/hide');
                }
            },
            maxWidth() {
                let width = 400;
                if(this.$vuetify.breakpoint.sm)
                    width = 450;
                else if(this.$vuetify.breakpoint.md)
                    width = 500;
                else if(this.$vuetify.breakpoint.lgAndUp)
                    width = 550;
                return width;
            }
        },
        methods: {

        }
    }
</script>

<style scoped>

</style>
