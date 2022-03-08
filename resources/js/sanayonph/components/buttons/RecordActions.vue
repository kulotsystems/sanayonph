<template>
    <v-row dense class="pt-3">
        <v-col cols="12" align="right">
            <button-action
                label="CANCEL"
                color="primary"
                rounded
                large
                text
                :disabled="btnAdd.loading || btnUpdate.loading"
                @click="$emit('cancel')"
            />
            <button-action
                icon="check"
                v-if="$route.name === addRoute"
                label="SAVE"
                color="primary"
                rounded
                large
                :loading="btnAdd.loading"
                @click="$emit('add')"
            />
            <button-action
                v-else-if="$route.name === editRoute"
                icon="check"
                label="UPDATE"
                color="primary"
                rounded
                large
                :loading="btnUpdate.loading"
                :disabled="!dirty"
                @click="$emit('update')"
            />
        </v-col>
    </v-row>
</template>

<script>
    export default {
        name: 'RecordActions',
        components: {
            'button-action': () => import('./ButtonAction.vue')
        },
        props: {
            btnAdd: {
                type: Object
            },
            btnUpdate: {
                type: Object
            },
            addRoute: {
                type: String
            },
            editRoute: {
                type: String
            },
            dirty: {
                type: Boolean
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
