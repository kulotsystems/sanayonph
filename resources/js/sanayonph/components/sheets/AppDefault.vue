<template>
    <v-app>
        <v-main>
            <v-container v-if="Object.values(errors).length > 0" fill-height fluid>
                <v-row align="center" justify="center">
                    <v-col align="center">
                        <h4 class="primary--text" v-if="message !== ''">{{ message }}</h4>
                        <div v-for="(error, field, index) in errors" :key="field">
                            <p class="error--text">{{  error[0] }}</p>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
            <v-container v-else fill-height fluid>
                <v-row align="center" justify="center">
                    <v-col align="center">
                        <loader-circular></loader-circular>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        name: 'AppDefault',
        components: {
            'loader-circular': () => import('../loaders/LoaderCircular.vue')
        },
        props: {
            message: {
                type   : String,
                default: ''
            },
            errors: {
                type   : Object,
                default: () => {
                    return {}
                }
            }
        },
        data() {
            return {}
        },
        computed: {},
        methods : {}
    }
</script>

<style scoped>

</style>
