<template>
    <v-app>
        <v-img
            :src="config.about"
        />
    </v-app>
</template>

<script>
    import aboutIMG from '../assets/img/app/about.png';

    export default {
        name: 'Demo',
        data() {
            return {
                config: {
                    about: aboutIMG
                }
            }
        }
    }
</script>
