<template>
    <v-app>
        <v-img
            :src="$vuetify.breakpoint.smAndDown ? config.mobile : config.desktop"
        />
    </v-app>
</template>

<script>
    import aboutDesktop from '../assets/img/app/about.png';
    import aboutMobile  from '../assets/img/app/about-mobile.png';

    export default {
        name: 'Demo',
        data() {
            return {
                config: {
                    desktop: aboutDesktop,
                    mobile : aboutMobile
                }
            }
        }
    }
</script>
