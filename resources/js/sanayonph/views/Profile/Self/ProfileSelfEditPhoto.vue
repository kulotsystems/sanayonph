<template>
    <div>
        <v-container>
            <dialog-cropper shape="circle" @crop="uploadPhoto"/>
            <v-row dense>
                <v-col cols="12" align="center">
                    <h3 class="mb-4 font-weight-thin primary--text" align="left">PROFILE</h3>
                    <v-avatar size="256" @click="showCropperDialog('user')">
                        <v-img
                            :lazy-src="$store.getters['auth/avatar'] !== '' ? $store.getters['path/userAvatar']['064'] + '/' + $store.getters['auth/avatar'] : $store.getters['path/defaultAvatar']"
                                 :src="$store.getters['auth/avatar'] !== '' ? $store.getters['path/userAvatar']['128'] + '/' + $store.getters['auth/avatar'] : $store.getters['path/defaultAvatar']"
                            aspect-ratio="1"
                        />
                    </v-avatar>
                    <div class="mt-5">
                        <button-action
                            label="CHANGE PROFILE PHOTO"
                            icon="camera"
                            @click="showCropperDialog('user')"
                            rounded
                            text
                        />
                    </div>
                </v-col>
            </v-row>
            <v-divider class="my-3"></v-divider>
            <v-row dense>
                <v-col cols="12" align="center">
                    <h3 class="mb-4 font-weight-thin primary--text" align="left">STORE</h3>
                    <v-avatar size="256" @click="showCropperDialog('store')">
                        <v-img
                            :lazy-src="$store.getters['auth/storeAvatar'] !== '' ? $store.getters['path/userAvatar']['064'] + '/' + $store.getters['auth/storeAvatar'] : $store.getters['path/defaultAvatar']"
                                 :src="$store.getters['auth/storeAvatar'] !== '' ? $store.getters['path/userAvatar']['128'] + '/' + $store.getters['auth/storeAvatar'] : $store.getters['path/defaultAvatar']"
                            aspect-ratio="1"
                        />
                    </v-avatar>
                    <div class="mt-5">
                        <button-action
                            label="CHANGE STORE PHOTO"
                            icon="camera"
                            @click="showCropperDialog('store')"
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
                config: {
                    target: ''
                },
                request: {

                },
                response: {
                    message: '',
                    errors : {}
                }
            }
        },
        computed: {},
        methods: {
            /****************************************************************************************************
             * METHOD: Show Cropper Dialog
             * Show the cropper dialog
             */
            showCropperDialog(target) {
                this.config.target = target;
                this.$store.commit('dialog/cropper/show');
            },

            /****************************************************************************************************
             * METHOD: UPLOAD PHOTO
             * Handle emitted image upload event
             */
            uploadPhoto(formData) {
                if(this.config.target === 'user') {
                    api_user.updateUserAvatar(formData).then(response => {
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
                else if(this.config.target === 'store') {
                    api_user.updateStoreAvatar(formData).then(response => {
                        if(!response) return;

                        this.$store.commit('auth/setStoreAvatar', response.data.file_name);
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
    }
</script>

<style scoped>

</style>
