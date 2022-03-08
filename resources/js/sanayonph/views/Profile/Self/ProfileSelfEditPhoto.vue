<template>
    <div>
        <v-container>
            <v-row dense>
                <v-col cols="12" align="center">
                    <dialog-cropper shape="circle" @crop="uploadPhoto"/>
                    <v-avatar size="256" @click="$store.commit('dialog/cropper/show')">
                        <v-img
                            :lazy-src="$store.getters['auth/avatar'] !== '' ? $store.getters['path/userAvatar']['064'] + '/' + $store.getters['auth/avatar'] : $store.getters['path/defaultAvatar']"
                                 :src="$store.getters['auth/avatar'] !== '' ? $store.getters['path/userAvatar']['128'] + '/' + $store.getters['auth/avatar'] : $store.getters['path/defaultAvatar']"
                            aspect-ratio="1"
                        />
                    </v-avatar>
                    <div class="mt-5">
                        <button-action
                            label="CHANGE PHOTO"
                            icon="camera"
                            @click="$store.commit('dialog/cropper/show')"
                            rounded
                            text
                        />
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
    import api_user from '../../../api/api-user.js';

    export default {
        name: 'ProfileSelfEditPhoto',
        components: {
            'dialog-cropper': () => import('../../../components/dialogs/DialogCropper.vue'),
            'button-action' : () => import('../../../components/buttons/ButtonAction.vue')
        },
        data() {
            return {
                response: {
                    message: '',
                    errors : {}
                }
            }
        },
        computed: {},
        methods: {
            /****************************************************************************************************
             * METHOD: UPLOAD PHOTO
             * Handle emitted image upload event
             */
            uploadPhoto(formData) {
                api_user.updateAvatar(formData).then(response => {
                    if(!response) return;

                    this.$store.commit('auth/setAvatar', response.data.file_name);
                    this.$store.commit('dialog/cropper/hide');
                }).catch(errors => {
                    this.$store.commit('dialog/cropper/load', false);
                    this.$store.commit('dialog/error/show', errors);
                    this.response.message = errors.response.data.message;
                    this.response.errors  = errors.response.data.errors;
                });
            }
        }
    }
</script>

<style scoped>

</style>
