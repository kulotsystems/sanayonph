<template>
    <v-menu
        v-model="config.isOpen"
        :close-on-content-click="false"
        :nudge-width="200"
        offset-x
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on" plain>
                <v-icon>more_vert</v-icon>
            </v-btn>
        </template>
        <v-card>
            <v-toolbar height="40" dense flat>
                <v-spacer></v-spacer>
                <v-btn class="mt-4" icon @click="config.isOpen = false">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-list nav dense>
                <v-list-item-group>
                    <v-list-item class="brown--text text-button">
                        <v-list-item-icon>
                            <v-icon color="brown">help</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title>
                            About
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item class="brown--text text-button">
                        <v-list-item-icon>
                            <v-icon color="brown">info</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title>
                            Terms of Use
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item class="brown--text text-button">
                        <v-list-item-icon>
                            <v-icon color="brown">admin_panel_settings</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title>
                            Privacy Policy
                        </v-list-item-title>
                    </v-list-item>
                </v-list-item-group>
                <v-list-item-group class="mt-2">
                    <v-list-item class="yellow brown--text" @click="signOut">
                        <v-list-item-icon>
                            <v-icon color="brown">{{ $store.getters['icon/state'].sign_out }}</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title>
                            SIGN OUT
                        </v-list-item-title>
                    </v-list-item>
                </v-list-item-group>
            </v-list>
        </v-card>
    </v-menu>
</template>

<script>
    import api_user from '../../../api/api-user.js';

    export default {
        name: 'MenuProfile',
        components: {},
        data() {
            return {
                config: {
                    isOpen: false
                }
            }
        },
        computed: {},
        methods: {
            signOut() {
                this.$store.commit('dialog/confirm/show', {
                    prompt: 'Do you really want to sign out?',
                    yesCallback: {
                        async : true,
                        action: () => {
                            api_user.signOut().then(response => {
								if(!response) return;

                                this.$emit('signedOut');
                            }).catch(errors => {
                                this.$emit('signedOut');
                            })
                        }
                    }
                });
            }
        }
    }
</script>

<style scoped>

</style>
