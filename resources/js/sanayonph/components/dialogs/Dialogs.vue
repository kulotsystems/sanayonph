<template>
    <div>
        <dialog-loader/>
        <dialog-message/>
        <dialog-confirm/>
        <dialog-error/>
        <dialog-image/>
    </div>
</template>

<script>
    export default {
        name: 'Dialogs',
        components: {
            'dialog-loader'  : () => import('./DialogLoader.vue'),
            'dialog-message' : () => import('./DialogMessage.vue'),
            'dialog-confirm' : () => import('./DialogConfirm.vue'),
            'dialog-error'   : () => import('./DialogError.vue'),
            'dialog-image'   : () => import('./DialogImage.vue')
        },
        props: {
            comingSoon: {
                type   : Boolean,
                default: false
            }
        },
        data() {
            return {}
        },
        computed: {},
        methods: {}
    }
</script>

<style scoped>

</style>
