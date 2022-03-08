<template>
    <v-dialog v-model="isOpen" :max-width="finalMaxWidth" :content-class="this.shape" :retain-focus="false" persistent>
        <v-card>
            <!-- CROPPER TITLE -->
            <v-card-title v-if="!plain" class="text-h5">
                <span v-html="this.title"></span>
                <v-spacer></v-spacer>
                <v-btn @click="close" :disabled="dialog.loading" icon>
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>

            <!-- CROPPER BODY -->
            <v-card-text align="center" class="px-0 pb-1" :class="{ 'pt-3': plain }">
                <input type="file" ref="input" accept=".jpg,.jpeg,.png,.JPG,.JPEG,.PNG" class="d-none" @change="change"/>
                <!--<div v-if="!plain" class="mb-2">
                    <v-btn @click="zoom('out')" :disabled="dialog.loading || !this.src" rounded text>
                        <v-icon left>zoom_out</v-icon>
                        ZOOM OUT
                    </v-btn>
                    <v-btn @click="zoom('in')" :disabled="dialog.loading || !this.src" rounded text>
                        <v-icon left>zoom_in</v-icon>
                        ZOOM IN
                    </v-btn>
                </div>-->
                <div v-show="src" :style="styleImageContainer">
                    <img ref="image" :src="src" alt="PHOTO" :style="styleImage"/>
                </div>
                <div v-if="!src" :style="styleImageContainer"></div>

                <div class="mt-2 px-2">
                    <span style="font-size: 11px;">This feature may not work on in-app browsers. Please use <span class="primary--text">Chrome</span> or <span class="primary--text">Firefox</span>.</span>
                </div>
            </v-card-text>

            <!-- CROPPER ACTIONS -->
            <v-card-actions>
                <v-btn @click="$refs.input.click()" class="primary" :disabled="dialog.loading || !ready">
                    BROWSE ..
                </v-btn>
                <v-btn v-if="!plain" text @click="clear" :disabled="dialog.loading || !this.src" rounded>
                    CLEAR
                </v-btn>
                <v-btn v-else text @click="close" :disabled="dialog.loading" rounded>
                    CLOSE
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn @click="crop" :loading="dialog.loading" :disabled="!this.src" rounded text>
                    <v-icon left>done</v-icon>
                    SAVE
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import 'cropperjs/dist/cropper.min.css';
    import Cropper from 'cropperjs';

    export default {
        name: 'DialogCropper',
        components: {},
        props: {
            title: {
                type   : String,
                default: 'CHANGE PHOTO'
            },
            shape: {
                type   : String,
                default: 'square'
            },
            width: {
                type   : Number,
                default: 256
            },
            height: {
                type   : Number,
                default: 256
            },
            maxWidth: {
                type   : Number,
                default: 288
            },
            plain: {
                type   : Boolean,
                default: false
            }
        },
        data() {
            return {
                cropper: null,
                file   : null,
                src    : null,
                ready  : false
            }
        },
        computed: {
            dialog() {
                return this.$store.getters['dialog/cropper/state'];
            },
            isOpen: {
                get() {
                    return this.$store.getters['dialog/cropper/state'].opened;
                },
                set() {
                    this.$store.commit('dialog/cropper/hide');
                }
            },
            finalWidth() {
                let width = this.width;
                if(this.width === this.height) {
                    if (this.$vuetify.breakpoint.sm)
                        width = Math.round(this.width * 1.1);
                    else if (this.$vuetify.breakpoint.md)
                        width = Math.round(this.width * 1.4);
                    else if (this.$vuetify.breakpoint.lgAndUp)
                        width = Math.round(this.width * 1.6);
                }
                return width;
            },
            finalHeight() {
                let height = this.height;
                if(this.width === this.height) {
                    if (this.$vuetify.breakpoint.sm)
                        height = Math.round(this.height * 1.1);
                    else if (this.$vuetify.breakpoint.md)
                        height = Math.round(this.height * 1.4);
                    else if (this.$vuetify.breakpoint.lgAndUp)
                        height = Math.round(this.height * 1.6);
                }
                return height;
            },
            finalMaxWidth() {
                let maxWidth = this.maxWidth;
                if(this.width === this.height) {
                    if (this.$vuetify.breakpoint.sm)
                        maxWidth = Math.round(this.maxWidth * 1.1);
                    else if (this.$vuetify.breakpoint.md)
                        maxWidth = Math.round(this.maxWidth * 1.4);
                    else if (this.$vuetify.breakpoint.lgAndUp)
                        maxWidth = Math.round(this.maxWidth * 1.6);
                }
                return maxWidth;
            },
            cropMultiplier() {
                let multiplier = 1;
                if(this.$vuetify.breakpoint.xs)
                    multiplier = 1.4;
                else if(this.$vuetify.breakpoint.sm)
                    multiplier = 1.27;
                return multiplier;
            },
            isLoading() {
                return this.$store.getters['dialog/cropper/state'].loading;
            },
            styleImageContainer() {
                return {
                    position: 'relative',
                    width   : `${this.finalWidth}px`,
                    height  : `${this.finalHeight}px`,
                    border  : '1px solid #eee',
                    overflow: 'hidden'
                }
            },
            styleImage() {
                return {
                    position: 'absolute',
                    top     : 0,
                    left    : 0,
                    width   : '100%'
                }
            }
        },
        methods: {

            /****************************************************************************************************
             * METHOD: INIT
             * Initialize the cropper object
             */
            init() {
                this.cropper = new Cropper(this.$refs.image, {
                    aspectRatio       : this.finalWidth / this.finalHeight,
                    minCropBoxWidth   : this.finalWidth,
                    minCropBoxHeight  : this.finalHeight,
                    minContainerWidth : this.finalWidth,
                    minContainerHeight: this.finalHeight,
                    viewMode          : 3,
                    dragMode          : 'move',
                    background        : false,
                    cropBoxMovable    : true,
                    cropBoxResizable  : false,
                });
            },

            /****************************************************************************************************
             * METHOD: CHANGE
             * Handle file input on-change event
             */
            change(e) {
                const files = e.target.files;
                if(files.length > 0) {
                    this.file = files[0];

                    let reader = new FileReader();
                    reader.readAsDataURL(this.file);
                    reader.onload = (e) => {
                        this.src = e.target.result;
                        this.cropper.replace(this.src);
                    }
                }
            },

            /****************************************************************************************************
             * METHOD: CLEAR
             * Clear image file
             */
            clear() {
                this.$refs.input.value = null;
                this.file  = null;
                this.src   = null;
                this.ready = false;
                if(this.cropper)
                    this.cropper.destroy();
            },

            /****************************************************************************************************
             * METHOD: CLOSE
             * Close the modal container
             */
            close() {
                this.clear();
                this.$store.commit('dialog/cropper/hide');
            },

            /****************************************************************************************************
             * METHOD: ZOOM
             * Zoom-in or zoom-out the image
             */
            zoom(mode) {
                if(this.src) {
                    if(mode === 'in')
                        this.cropper.zoom(0.1);
                    else if(mode === 'out')
                        this.cropper.zoom(-0.1);
                }
            },

            /****************************************************************************************************
             * METHOD: CROP
             * Crop the image and emit the form-data on a 'crop' event
             */
            crop() {
                if(this.src) {
                    this.cropper.getCroppedCanvas({
                        width : Math.round(this.finalWidth  * this.cropMultiplier),
                        height: Math.round(this.finalHeight * this.cropMultiplier),
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high'
                    }).toBlob((blob) => {
                        const formData = new FormData();
                        formData.append('file', blob, this.file.name);

                        this.$store.commit('dialog/cropper/load');
                        this.$emit('crop', formData);
                    }, 'image/jpeg', 1);
                }
            }
        },
        watch: {

            /****************************************************************************************************
             * WATCH: isOpen
             * Track changes to the 'isOpen' property
             */
            isOpen() {
                if(this.isOpen) {
                    setTimeout(() => {
                        this.init();
                        if(this.cropper)
                            this.cropper.enable();
                        this.ready = true;
                    }, 120);
                }
                else
                    this.clear();
            },

            /****************************************************************************************************
             * WATCH: isLoading
             * Track if dialog-cropper is loading or not
             */
            isLoading() {
                if (this.isLoading) {
                    try {
                        this.cropper.disable();
                    } catch(e) { }
                }
                else {
                    try {
                        this.cropper.enable();
                    } catch(e) { }
                }
            },


            /****************************************************************************************************
             * WATCH: finalMaxWidth
             * Track if window is resized
             */
            finalMaxWidth() {
                if(this.cropper) {
                    this.cropper.minCropBoxWidth    = this.finalWidth;
                    this.cropper.minCropBoxHeight   = this.finalHeight;
                    this.cropper.minContainerWidth  = this.finalMaxWidth;
                    this.cropper.minContainerHeight = this.finalHeight;
                }
            }
        },
        mounted() {
            // this.init();
        },
    }
</script>

<style scoped>

</style>
