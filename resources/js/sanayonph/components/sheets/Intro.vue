<template>
    <div>
        <div v-if="!ready">
            <table v-if="model < 0" style="position: fixed; width: 100%; height: 100%;" @click="model = 0">
                <tr>
                    <td align="center">
                        <v-avatar size="256">
                            <v-img :src="logo" :alt="$store.getters['env/state'].app.name" aspect-ratio="1"/>
                        </v-avatar>
                    </td>
                </tr>
            </table>
            <div v-else>
                <v-carousel v-model="model" :show-arrows="false" hide-delimiter-background height="380">
                    <v-carousel-item v-for="(slide, i) in slides" :key="i">
                        <table style="width: 100%; height: 100%">
                            <tr>
                                <td align="center">
                                    <v-avatar size="160" rounded="0">
                                        <v-img :src="slide.image" aspect-ratio="1"/>
                                    </v-avatar>
                                    <h2 class="text-center font-weight-thin secondary--text mt-2" v-html="slide.title"></h2>
                                </td>
                            </tr>
                        </table>
                    </v-carousel-item>
                </v-carousel>

                <v-card class="text-center px-3 mt-5 secondary--text font-weight-thin transparent" flat>
                    {{ slides[model].subtitle }}
                    <p v-if="model === 0" class="mt-4">
                        Already a member? <v-btn depressed color="primary" style="padding-left: 4px; padding-right: 4px;" text small @click="signIn">Sign In</v-btn>
                    </p>
                    <p v-else-if="model === slides.length-1" class="mt-4">
                        Don't have an account? <v-btn depressed color="primary" style="padding-left: 4px; padding-right: 4px;" text small exact router :to="{ name: 'sign-up' }">Sign Up</v-btn>
                    </p>
                </v-card>

                <v-bottom-navigation class="block" grow app mandatory>
                    <button-action
                        label="SKIP"
                        block
                        large
                        @click="skip"
                        color="primary"
                        class-name="white--text text-body-1"
                    />
                </v-bottom-navigation>
            </div>
        </div>
        <!--<div v-else>
            <table style="position: fixed; width: 100%; height: 100%;" @click="model = 0">
                <tr>
                    <td align="center">
                        <v-avatar size="256">
                            <v-img :src="loading"/>
                        </v-avatar>
                    </td>
                </tr>
            </table>
        </div>-->
    </div>
</template>

<script>
    import logo    from '../../assets/img/logo.jpg';
    import boost   from '../../assets/img/intro/boost.png';
    import coin    from '../../assets/img/intro/coin.png';
    import support from '../../assets/img/intro/support.png';
    import dagos   from '../../assets/img/intro/dagos.png';
    // import loading from '../../assets/img/intro/5-loading.png';

    export default {
        name: 'Intro',
        components: {
            'button-action': () => import('../../components/buttons/ButtonAction.vue')
        },
        data() {
            return {
                logo   : logo,
                // loading: loading,
                slides: [
                    {
                        image   : boost,
                        title   : '"Boost your origins just<br>a click away"',
                        subtitle: ''
                    },
                    {
                        image   : coin,
                        title   : '"Experience<br>cost-effective transactions"',
                        subtitle: 'Be our partner merchant'
                    },
                    {
                        image   : support,
                        title   : '"Be a <br>\'local-made\' advocate"',
                        subtitle: 'Select among our locally produced varieties with love.',
                    },
                    {
                        image   : dagos,
                        title   : 'DAGOS PO KAMO<br>MGA KA-NAYON!',
                        subtitle: 'Be a part of our community',
                    },
                ],
                model: -1,
                ready: false,
            }
        },
        computed: {},
        methods: {
            prev() {
                if(this.model > 0)
                    this.model -= 1;
            },
            next() {
                if(this.model < (this.slides.length - 1))
                    this.model += 1;
                else
                    this.skip();
            },
            skip() {
                this.ready = true;
                // setTimeout(() => {
                    this.$emit('skipIntro');
                // }, 500);
            },
            signIn() {
                this.ready = true;
                this.$emit('skipIntro');
            }
        },
        mounted() {
            setTimeout(() => {
                if(this.model < 0)
                    this.model = 0;
            }, 2000);
        }
    }
</script>

<style>
    .v-carousel__controls__item {
        color: #b75c08 !important;
        height: 23px !important;
        width: 23px !important;
    }

    .v-carousel__controls__item svg {
        fill: #fae750 !important;
    }

    .v-carousel__controls__item.v-item--active svg {
        fill: #b75c08 !important;
    }

    .v-carousel__controls__item:before {
        opacity: .18;
    }

    .v-carousel__controls__item.v-item--active:before {
        opacity: .28;
    }
</style>
