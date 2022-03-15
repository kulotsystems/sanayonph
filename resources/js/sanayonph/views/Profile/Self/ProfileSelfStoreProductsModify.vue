<template>
    <v-app>
        <dialogs/>
        <dialog-cropper :width="300" :height="300" :maxWidth="330" @crop="uploadPhoto"/>
        <snackbar-bottom/>
        <toolbar-main>
            <tab-product-modify slot="tabs"/>
            <button-action slot="action"      v-if="$route.name === config.addRoute"  label="SAVE"   icon="save" rounded text outlined :loading="config.btnAdd.loading"    :disabled="!dirty" @click="storeProduct" />
            <button-action slot="action" v-else-if="$route.name === config.editRoute" label="UPDATE" icon="save" rounded text outlined :loading="config.btnUpdate.loading" :disabled="!dirty" @click="updateProduct"/>
        </toolbar-main>
        <v-main class="secondary">
            <bg-secondary/>
            <v-container class="pa-2 pa-sm-3">
                <v-row class="justify-center">
                    <v-col cols="12" sm="12" md="8">
                        <v-tabs-items v-model="$store.state['tab/' + $route.name]" class="transparent" @change="changeTab($event)">
                            <!-- GENERAL -->
                            <v-tab-item id="general">
                                <v-form ref="generalForm">
                                    <v-card flat :loading="config.cardLoading">
                                        <v-toolbar class="grey lighten-5" dense flat>
                                            <v-icon class="grey--text text--lighten-1 mr-1" small>{{ $store.getters['icon/state'].product }}</v-icon>
                                            <span class="v-toolbar-text text-body font-weight-light grey--text">General Product Info</span>
                                        </v-toolbar>

                                        <v-divider></v-divider>

                                        <v-card-text>
                                            <!-- PRODUCT CATEGORY -->
                                            <v-row dense>
                                                <v-col cols="12">
                                                    <v-autocomplete
                                                        label="Category"
                                                        v-model="request.category"
                                                        class="text-body-1 uppercase"
                                                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response, 'category')]"
                                                        @change="$store.dispatch('form/reset', [response, 'category']).then(r => response)"
                                                        :items="config.category.items"
                                                        :item-text="item  => `${item.name}`"
                                                        :item-value="item => `${item.id}`"
                                                        :loading="config.category.loading"
                                                        :disabled="config.category.disabled || config.btnAdd.loading || config.btnUpdate.loading"
                                                        autocomplete="off"
                                                        :prepend-inner-icon="$store.getters['icon/state'].category"
                                                    />
                                                </v-col>
                                            </v-row>

                                            <!-- PRODUCT NAME -->
                                            <v-row dense>
                                                <v-col cols="12">
                                                    <v-text-field
                                                        label="Name"
                                                        v-model="request.name"
                                                        :counter="config.name.max"
                                                        :rules="[$store.state.form.rules.required, $store.state.form.rules.max_chars(request.name, config.name.max), $store.state.form.rules.feedback(response, 'name')]"
                                                        @keyup="$store.dispatch('form/reset', [response, 'name']).then(r => response)"
                                                        class="text-h6"
                                                        :loading="config.name.loading"
                                                        :disabled="config.name.loading || config.btnAdd.loading || config.btnUpdate.loading"
                                                        autocomplete="off"
                                                    />
                                                </v-col>
                                            </v-row>

                                            <!-- PRODUCT DESCRIPTION -->
                                            <v-row dense>
                                                <v-col cols="12">
                                                    <v-textarea
                                                        label="Description"
                                                        v-model="request.description"
                                                        :counter="config.description.max"
                                                        :rules="[$store.state.form.rules.max_chars(request.description, config.description.max), $store.state.form.rules.feedback(response, 'description')]"
                                                        @keyup="$store.dispatch('form/reset', [response, 'description']).then(r => response)"
                                                        class="text-body-1 uppercase"
                                                        :loading="config.description.loading"
                                                        :disabled="config.name.loading || config.btnAdd.loading || config.btnUpdate.loading"
                                                        autocomplete="off"
                                                        auto-grow
                                                    />
                                                </v-col>
                                            </v-row>

                                            <!-- PRODUCT DELIVERY ORIGIN -->
                                            <v-row dense>
                                                <v-col cols="12">
                                                    <v-autocomplete
                                                        label="Delivery Origin"
                                                        v-model="request.delivery_origin"
                                                        class="text-body-1 uppercase"
                                                        :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response, 'delivery_origin')]"
                                                        @change="$store.dispatch('form/reset', [response, 'delivery_origin']).then(r => response)"
                                                        :items="config.delivery_origin.items"
                                                        :item-text="item  => `${item.address}`"
                                                        :item-value="item => `${item.id}`"
                                                        :loading="config.delivery_origin.loading"
                                                        :disabled="config.delivery_origin.disabled || config.btnAdd.loading || config.btnUpdate.loading"
                                                        autocomplete="off"
                                                        :prepend-inner-icon="$store.getters['icon/state'].address"
                                                    />

                                                    <!-- PRODUCT ALLOW PICKUP -->
                                                    <v-switch
                                                        flat
                                                        label="Allow for meetup or pickup arrangement."
                                                        v-model="request.allows_pickup"
                                                        color="primary"
                                                        class="mt-0"
                                                        @change="allowPickupChange"
                                                    />
                                                </v-col>
                                            </v-row>

                                            <!-- PRODUCT GENERAL IMAGES -->
                                            <transition-group name="list-complete" tag="v-row" class="mt-3">
                                                <v-col class="pa-2 pt-2" cols="6" sm="4" md="3" v-for="(image, index) in request.images" :key="image.id" :class="{ 'list-complete-item': config.animation }">
                                                    <v-card>
                                                        <v-img
                                                            :lazy-src="$store.getters['path/defaultProduct']"
                                                                 :src="image.image != null ? $store.getters['path/productImg'][imageDir] + '/' + image.image  : $store.getters['path/defaultProduct']"
                                                            aspect-ratio="1"
                                                        >
                                                            <template v-slot:placeholder>
                                                                <v-row class="fill-height ma-0" align="center" justify="center">
                                                                    <v-progress-circular indeterminate color="primary lighten-3"/>
                                                                </v-row>
                                                            </template>
                                                        </v-img>
                                                        <v-card-actions>
                                                            <v-spacer></v-spacer>
                                                            <v-btn fab class="primary lighten-1" depressed x-small @click="showUploadCropperDialog(index, null)">
                                                                <v-icon>insert_photo</v-icon>
                                                            </v-btn>
                                                            <v-btn fab class="primary lighten-1" depressed x-small @click="showDeleteConfirmDialog(index, null)" v-if="index !== request.images.length-1">
                                                                <v-icon>delete</v-icon>
                                                            </v-btn>
                                                        </v-card-actions>
                                                    </v-card>
                                                </v-col>
                                            </transition-group>
                                            <!--<v-row dense class="pt-3">
                                                <v-col cols="12" align="center">
                                                    <v-btn color="primary" text large @click="addImage">
                                                        <v-icon left>add</v-icon>
                                                        ADD PHOTO
                                                    </v-btn>
                                                </v-col>
                                            </v-row>-->
                                        </v-card-text>

                                        <!-- RECORD ACTIONS (GENERAL) -->
                                        <v-card-actions>
                                            <record-actions
                                                :btn-add="config.btnAdd"
                                                :btn-update="config.btnUpdate"
                                                :add-route="config.addRoute"
                                                :edit-route="config.editRoute"
                                                :dirty="dirty"
                                                @add="storeProduct"
                                                @update="updateProduct"
                                                @cancel="goBack"
                                            />
                                        </v-card-actions>
                                    </v-card>
                                </v-form>
                            </v-tab-item>

                            <!-- VARIATIONS -->
                            <v-tab-item id="variations">
                                <v-form ref="variationsForm">
                                    <v-card flat :loading="config.cardLoading">
                                        <v-toolbar class="grey lighten-5" dense flat>
                                            <v-icon class="grey--text text--lighten-1 mr-1" small>{{ $store.getters['icon/state'].product }}</v-icon>
                                            <span class="v-toolbar-text text-body font-weight-light grey--text">Product Variations</span>
                                        </v-toolbar>

                                        <v-divider></v-divider>

                                        <v-card-text class="pa-0">

                                            <!-- VARIATION 1 :: (COLOR) -->
                                            <v-card flat>
                                                <v-card-text>
                                                    <v-row dense>
                                                        <v-col cols="12">
                                                            <v-text-field
                                                                label="Variation #1"
                                                                v-model="request.variations[0].title"
                                                                :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response, 'variations.0.title')]"
                                                                @keyup="$store.dispatch('form/reset', [response, 'variations.0.title']).then(r => response)"
                                                                class="text-h6 uppercase"
                                                                autocomplete="off"
                                                                :readonly="config.variations[0].readonly"
                                                                :outlined="!config.variations[0].readonly"
                                                                :dense="!config.variations[0].readonly"
                                                                :append-icon="config.variations[0].readonly ? 'edit' : 'done'"
                                                                @click:append="config.variations[0].readonly = !config.variations[0].readonly"
                                                                @blur="config.variations[0].readonly = true"
                                                            />
                                                        </v-col>
                                                    </v-row>

                                                    <!-- VARIATION 1 ITEMS -->
                                                    <transition-group name="list-complete" tag="v-row">
                                                        <v-col class="pa-2 pt-2" cols="6" sm="4" md="3" v-for="(item, index) in request.variations[0].items" :key="item.id" :class="{ 'list-complete-item': config.animation }">
                                                            <v-card>
                                                                <v-img
                                                                    :lazy-src="$store.getters['path/defaultProduct']"
                                                                         :src="item.image != null ? $store.getters['path/productImg'][imageDir] + '/' + item.image : $store.getters['path/defaultProduct']"
                                                                    aspect-ratio="1"
                                                                >
                                                                    <template v-slot:placeholder>
                                                                        <v-row class="fill-height ma-0" align="center" justify="center">
                                                                            <v-progress-circular indeterminate color="primary lighten-3"/>
                                                                        </v-row>
                                                                    </template>
                                                                </v-img>
                                                                <v-card-text>
                                                                    <v-text-field
                                                                        v-model="item.label"
                                                                        :label="request.variations[0].title.toUpperCase()"
                                                                        :rules="[$store.state.form.rules.required]"
                                                                        class="centered uppercase bold"
                                                                        outlined
                                                                        dense
                                                                    />
                                                                </v-card-text>
                                                                <v-card-actions>
                                                                    <v-spacer></v-spacer>
                                                                    <v-btn fab class="primary lighten-1" depressed x-small @click="showUploadCropperDialog(index, 0)">
                                                                        <v-icon>insert_photo</v-icon>
                                                                    </v-btn>
                                                                    <v-btn fab class="primary lighten-1" depressed x-small @click="showDeleteConfirmDialog(index, 0)">
                                                                        <v-icon>delete</v-icon>
                                                                    </v-btn>
                                                                </v-card-actions>
                                                            </v-card>
                                                        </v-col>
                                                    </transition-group>
                                                </v-card-text>

                                                <v-card-actions>
                                                    <v-row dense class="pt-3">
                                                        <v-col cols="12" align="center">
                                                            <v-btn color="primary" text large @click="addItem(0)">
                                                                <v-icon left>add</v-icon>
                                                                ADD<template v-if="request.variations[0].title.trim() !== ''"> {{ request.variations[0].title.trim() }}</template>
                                                            </v-btn>
                                                        </v-col>
                                                    </v-row>
                                                </v-card-actions>
                                            </v-card>

                                            <v-divider class="mt-5 mb-5"></v-divider>

                                            <!-- VARIATION 2 :: (SIZE) -->
                                            <v-card flat>
                                                <v-card-text>
                                                    <v-row dense>
                                                        <v-col cols="12">
                                                            <v-text-field
                                                                label="Variation #2"
                                                                v-model="request.variations[1].title"
                                                                :rules="[$store.state.form.rules.required, $store.state.form.rules.feedback(response, 'variations.1.title')]"
                                                                @keyup="$store.dispatch('form/reset', [response, 'variations.1.title']).then(r => response)"
                                                                class="text-h6 uppercase"
                                                                autocomplete="off"
                                                                :readonly="config.variations[1].readonly"
                                                                :outlined="!config.variations[1].readonly"
                                                                :dense="!config.variations[1].readonly"
                                                                :append-icon="config.variations[1].readonly ? 'edit' : 'done'"
                                                                @click:append="config.variations[1].readonly = !config.variations[1].readonly"
                                                                @blur="config.variations[1].readonly = true"
                                                            />
                                                        </v-col>
                                                    </v-row>

                                                    <!-- VARIATION 2 ITEMS -->
                                                    <transition-group name="list-complete" tag="v-row">
                                                        <v-col class="pa-2 pt-2"cols="6" sm="4" md="3" v-for="(item, index) in request.variations[1].items" :key="item.id" :class="{ 'list-complete-item': config.animation }">
                                                            <v-card>
                                                                <v-img
                                                                    :lazy-src="$store.getters['path/defaultProduct']"
                                                                         :src="item.image != null ? $store.getters['path/productImg'][imageDir] + '/' + item.image : $store.getters['path/defaultProduct']"
                                                                    aspect-ratio="1"
                                                                >
                                                                    <template v-slot:placeholder>
                                                                        <v-row class="fill-height ma-0" align="center" justify="center">
                                                                            <v-progress-circular indeterminate color="primary lighten-3"/>
                                                                        </v-row>
                                                                    </template>
                                                                </v-img>
                                                                <v-card-text>
                                                                    <v-text-field
                                                                        v-model="item.label"
                                                                        :label="request.variations[1].title.toUpperCase()"
                                                                        :rules="[$store.state.form.rules.required]"
                                                                        class="centered uppercase bold"
                                                                        outlined
                                                                        dense
                                                                    />
                                                                </v-card-text>
                                                                <v-card-actions>
                                                                    <v-spacer></v-spacer>
                                                                    <v-btn fab class="primary lighten-1" depressed x-small @click="showUploadCropperDialog(index, 1)">
                                                                        <v-icon>insert_photo</v-icon>
                                                                    </v-btn>
                                                                    <v-btn fab class="primary lighten-1" depressed x-small @click="showDeleteConfirmDialog(index, 1)">
                                                                        <v-icon>delete</v-icon>
                                                                    </v-btn>
                                                                </v-card-actions>
                                                            </v-card>
                                                        </v-col>
                                                    </transition-group>
                                                </v-card-text>
                                                <v-card-actions>
                                                    <v-row dense class="pt-3">
                                                        <v-col cols="12" align="center">
                                                            <v-btn color="primary" text large @click="addItem(1)">
                                                                <v-icon left>add</v-icon>
                                                                ADD<template v-if="request.variations[1].title.trim() !== ''"> {{ request.variations[1].title.trim() }}</template>
                                                            </v-btn>
                                                        </v-col>
                                                    </v-row>
                                                </v-card-actions>
                                            </v-card>
                                        </v-card-text>

                                        <!-- RECORD ACTIONS (VARIATIONS) -->
                                        <v-card-actions>
                                            <record-actions
                                                :btn-add="config.btnAdd"
                                                :btn-update="config.btnUpdate"
                                                :add-route="config.addRoute"
                                                :edit-route="config.editRoute"
                                                :dirty="dirty"
                                                @add="storeProduct"
                                                @update="updateProduct"
                                                @cancel="goBack"
                                            />
                                        </v-card-actions>
                                    </v-card>
                                </v-form>
                            </v-tab-item>

                            <!-- PRICE AND STOCK -->
                            <v-tab-item id="price_stock">
                                <v-form ref="priceStockForm">
                                    <v-card flat :loading="config.cardLoading">
                                        <v-toolbar class="grey lighten-5" dense flat>
                                            <v-icon class="grey--text text--lighten-1 mr-1" small>{{ $store.getters['icon/state'].product }}</v-icon>
                                            <span class="v-toolbar-text text-body font-weight-light grey--text">Price and Stock</span>
                                        </v-toolbar>

                                        <v-divider></v-divider>

                                        <v-card-text>
                                            <!-- variation1 only -->
                                            <template v-if="variationMode === 'var1_only'">
                                                <table style="width: 100%;">
                                                    <thead>
                                                        <th></th>
                                                        <th style="width: 40%">
                                                            {{ request.variations[0].title.toUpperCase() }}
                                                        </th>
                                                        <th>PRICE</th>
                                                        <th>STOCK</th>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(variation, index) in request.variations[0].items" :key="index">
                                                            <td align="right"><small>{{ index + 1 }}.</small></td>
                                                            <td class="primary--text" align="center">{{ variation.label.toUpperCase() }}</td>
                                                            <td class="px-2">
                                                                <v-text-field
                                                                    class="right"
                                                                    type="number"
                                                                    v-model.number="request.price.var1_only[`${index}`]"
                                                                    :rules="[$store.state.form.rules.min_value(request.price.var1_only[`${index}`], 1, 'price')]"
                                                                    @keyup="forceCache"
                                                                />
                                                            </td>
                                                            <td class="px-2">
                                                                <v-text-field
                                                                    class="right"
                                                                    type="number"
                                                                    v-model.number="request.stock.var1_only[`${index}`]"
                                                                    :rules="[$store.state.form.rules.min_value(request.stock.var1_only[`${index}`], 0, 'stock')]"
                                                                    @keyup="forceCache"
                                                                />
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </template>

                                            <!-- variation2 only -->
                                            <template v-else-if="variationMode === 'var2_only'">
                                                <table style="width: 100%;">
                                                    <thead>
                                                        <th></th>
                                                        <th style="width: 40%;">
                                                            {{ request.variations[1].title.toUpperCase() }}
                                                        </th>
                                                        <th>PRICE</th>
                                                        <th>STOCK</th>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(variation, index) in request.variations[1].items" :key="index">
                                                            <td align="right"><small>{{ index + 1 }}.</small></td>
                                                            <td class="primary--text" align="center">{{ variation.label.toUpperCase() }}</td>
                                                            <td class="px-2">
                                                                <v-text-field
                                                                    class="right"
                                                                    type="number"
                                                                    v-model.number="request.price.var2_only[`${index}`]"
                                                                    :rules="[$store.state.form.rules.min_value(request.price.var2_only[`${index}`], 1, 'price')]"
                                                                    @keyup="forceCache"
                                                                />
                                                            </td>
                                                            <td class="px-2">
                                                                <v-text-field
                                                                    class="right"
                                                                    type="number"
                                                                    v-model.number="request.stock.var2_only[`${index}`]"
                                                                    :rules="[$store.state.form.rules.min_value(request.stock.var2_only[`${index}`], 0, 'stock')]"
                                                                    @keyup="forceCache"
                                                                />
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </template>

                                            <!-- both variations 1 and 2 -->
                                            <template v-else-if="variationMode === 'both_vars'">
                                                <table style="width: 100%;">
                                                    <thead>
                                                        <th></th>
                                                        <th style="width: 20%;">{{ request.variations[0].title.toUpperCase() }}</th>
                                                        <th style="width: 20%;">{{ request.variations[1].title.toUpperCase() }}</th>
                                                        <th>PRICE</th>
                                                        <th>STOCK</th>
                                                    </thead>
                                                    <tbody>
                                                        <template v-for="(variation1, index1) in request.variations[0].items">
                                                            <template v-for="(variation2, index2) in request.variations[1].items">
                                                                <tr>
                                                                    <td align="right"><small>{{ 1 + index1 + index2 + (index1 * (request.variations[1].items.length - 1)) }}.</small></td>
                                                                    <td class="primary--text" align="center">{{ variation1.label.toUpperCase() }}</td>
                                                                    <td class="primary--text" align="center">{{ variation2.label.toUpperCase() }}</td>
                                                                    <td class="px-2">
                                                                        <v-text-field
                                                                            class="right"
                                                                            type="number"
                                                                            v-model.number="request.price.both_vars[`${index1}_${index2}`]"
                                                                            :rules="[$store.state.form.rules.min_value(request.price.both_vars[`${index1}_${index2}`], 1, 'price')]"
                                                                            @keyup="forceCache"
                                                                        />
                                                                    </td>
                                                                    <td class="px-2">
                                                                        <v-text-field
                                                                            class="right"
                                                                            type="number"
                                                                            v-model.number="request.stock.both_vars[`${index1}_${index2}`]"
                                                                            :rules="[$store.state.form.rules.min_value(request.stock.both_vars[`${index1}_${index2}`], 0, 'stock')]"
                                                                            @keyup="forceCache"
                                                                        />
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </template>

                                            <!-- empty variations -->
                                            <template v-else-if="variationMode === 'no_vars'">
                                                <v-alert prominent outlined color="primary" class="mb-0">
                                                    <v-row align="center">
                                                        <v-col class="grow">
                                                            Please add at least one product variation first.
                                                        </v-col>
                                                        <v-col class="shrink">
                                                            <v-btn color="primary" @click="changeTab('variations')">Variations</v-btn>
                                                        </v-col>
                                                    </v-row>
                                                </v-alert>
                                            </template>
                                        </v-card-text>

                                        <!-- RECORD ACTIONS (PRICE & STOCK) -->
                                        <v-card-actions>
                                            <record-actions
                                                :btn-add="config.btnAdd"
                                                :btn-update="config.btnUpdate"
                                                :add-route="config.addRoute"
                                                :edit-route="config.editRoute"
                                                :dirty="dirty"
                                                @add="storeProduct"
                                                @update="updateProduct"
                                                @cancel="goBack"
                                            />
                                        </v-card-actions>
                                    </v-card>
                                </v-form>
                            </v-tab-item>
                        </v-tabs-items>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    import api_product from '../../../api/api-product.js';

    export default {
        name: 'ProfileSelfStoreProductsModify',
        components: {
            'bg-secondary'      : () => import('../../../components/backgrounds/BackgroundSecondary.vue'),
            'dialogs'           : () => import('../../../components/dialogs/Dialogs.vue'),
            'dialog-cropper'    : () => import('../../../components/dialogs/DialogCropper.vue'),
            'snackbar-bottom'   : () => import('../../../components/snackbars/SnackbarBottom.vue'),
            'toolbar-main'      : () => import('../../../components/toolbars/ToolbarMain.vue'),
            'tab-product-modify': () => import('../../../components/toolbars/tabs/TabProductModify.vue'),
            'record-actions'    : () => import('../../../components/buttons/RecordActions.vue'),
            'button-action'     : () => import('../../../components/buttons/ButtonAction.vue')
        },
        data() {
            return {
                config: {
                    animation : false,
                    addRoute  : 'profile-store-products-add',
                    editRoute : 'profile-store-products-edit',
                    category  : {
                        items   : [],
                        loading : false
                    },
                    delivery_origin: {
                        items   : [],
                        loading : false,
                        disabled: false
                    },
                    name: {
                        loading: false,
                        max    : 50,
                    },
                    description: {
                        loading: false,
                        max    : 2000
                    },
                    variations: [
                        {
                            readonly   : true,
                            activeIndex: -1
                        },
                        {
                            readonly   : true,
                            activeIndex: -1
                        }
                    ],
                    activeVariation: -1,
                    activePhoto    : -1,
                    btnAdd: {
                        loading : false,
                    },
                    btnUpdate: {
                        loading : false,
                    },
                    showActions: false,
                    cardLoading: false
                },
                request: {
                    category       : 0,
                    name           : '',
                    description    : '',
                    delivery_origin: 0,
                    allows_pickup  : true,
                    images         : [],
                    variations: [
                        {
                            title: 'COLOR',
                            items: []
                        },
                        {
                            title: 'SIZE',
                            items: []
                        }
                    ],
                    price: {
                        var1_only: {},
                        var2_only: {},
                        both_vars: {},
                    },
                    stock: {
                        var1_only: {},
                        var2_only: {},
                        both_vars: {},
                    },
                    price_stock_mode: ''
                },
                response: {
                    message: '',
                    errors : {}
                },
                cacheCTR: 1
            }
        },
        computed: {
            dirty() {
                let temp = this.cacheCTR;
                return this.cache !== JSON.stringify(this.request);
            },
            variationMode() {
                // 'var1_only' | 'var2_only' | 'both_vars' | 'no-vars'
                let type = 'no_vars';
                if(this.request.variations[0].items.length > 0) {
                    if(this.request.variations[1].items.length <= 0)
                        type = 'var1_only';
                    else
                        type = 'both_vars'
                }
                else if(this.request.variations[1].items.length > 0)
                    type = 'var2_only'
                return type;
            },

            // computed image directory
            imageDir() {
                return this.$vuetify.breakpoint.xs ? '128' : '300';
            }
        },
        methods: {
            /****************************************************************************************************
             * METHOD: FORCE CACHE
             * Force dirty() computed property to evaluate
             */
            forceCache() {
                this.cacheCTR += 1;
            },

            /****************************************************************************************************
             * METHOD: GO BACK
             * Navigate back to 'profile-store-products' view
             */
            goBack() {
                this.$store.commit('history/goBack', {
                    navRight: {name: this.$route.name, params: this.$route?.params},
                    navLeft : {name: 'profile-store-products'}
                });
            },

            /****************************************************************************************************
             * METHOD: ON LOAD
             * Get all available categories or product details
             */
            onLoad() {
                if(this.$route.name === this.config.addRoute) {
                    this.config.category.loading        = true;
                    this.config.delivery_origin.loading = true;
                    api_product.dependencies().then(response => {
                        if(!response) return;

                        if(response.data.categories.length <= 0) {
                            this.$store.commit('dialog/message/show', {
                                title  : 'Notice',
                                prompt : 'Please set at least one <span class="primary--text">Product Category</span> first.',
                                okIcon : this.$store.getters['icon/state'].category,
                                okLabel: 'Categories',
                                callback: {
                                    action: () => {
                                        this.$router.replace({ name: 'profile-store-categories' });
                                    }
                                },
                                persistent: true
                            });
                        }
                        else {
                            this.config.category.items   = response.data.categories;
                            this.config.category.loading = false;

                            if(response.data.delivery_origins <= 0) {
                                this.$store.commit('dialog/message/show', {
                                    title : 'Notice',
                                    prompt: 'Please set at least one <span class="primary--text">Delivery Address</span> first.',
                                    okIcon : this.$store.getters['icon/state'].address,
                                    okLabel: 'Addresses',
                                    callback: {
                                        action: () => {
                                            this.$router.replace({ name: 'profile-delivery-addresses' });
                                        }
                                    },
                                    persistent: true
                                });
                            }
                            else {
                                this.config.delivery_origin.items   = response.data.delivery_origins;
                                this.config.delivery_origin.loading = false;
                            }
                        }
                    }).catch(errors => {
                        this.$store.commit('dialog/error/show', errors);
                    });

                    // append an empty product image
                    this.addImage();
                }
                else if(this.$route.name === this.config.editRoute) {
                    this.config.cardLoading = true;
                    api_product.edit(this.$route.params.product).then(response => {
                        if(!response) return;

                        if(response.data.product != null) {
                            let product = response.data.product;

                            // extract categories
                            this.config.category.items = response.data.categories;

                            // extract category
                            this.request.category = product.category_id.toString();

                            // extract delivery origins
                            this.config.delivery_origin.items = response.data.delivery_origins;

                            // extract delivery origin
                            this.request.delivery_origin = product.delivery_origin.toString();

                            // extract product general details
                            this.request.name          = product.name;
                            this.request.description   = product.description;
                            this.request.allows_pickup = product.allows_pickup;

                            // extract general images
                            for(let i=0; i<product.gen_images.length; i++) {
                                this.request.images.push({
                                    id   : i,
                                    image: product.gen_images[i]
                                });
                            }

                            // extract variations
                            this.request.variations = product.variations;

                            // extract price and stock
                            this.request.price_stock_mode = product.price_stock_mode;
                            for(let i=0; i<product.prices_stocks.length; i++) {
                                let price_stock      = product.prices_stocks[i];
                                let price_stock_mode = price_stock.price_stock_mode;
                                let key_pair         = `${price_stock.var1_item_index}_${price_stock.var2_item_index}`;
                                if(price_stock_mode === 'var1_only')
                                    key_pair = `${price_stock.var1_item_index}`;
                                else if(price_stock_mode === 'var2_only')
                                    key_pair = `${price_stock.var2_item_index}`;
                                this.request.price[price_stock_mode][key_pair] = price_stock.price;
                                this.request.stock[price_stock_mode][key_pair] = price_stock.stock;
                            }

                            // remove card loading, store cache
                            this.config.cardLoading = false;

                            // append an empty product image
                            this.addImage();

                            // cache
                            this.cache = JSON.stringify(this.request);
                            this.forceCache();
                        }
                    }).catch(errors => {
                        this.config.name.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                    });
                }
            },

            /****************************************************************************************************
             * METHOD: CHANGE TAB
             * Handle v-tabs-item change
             */
            changeTab(tabID) {
                if(this.$route.params.tab !== tabID) {
                    this.$router.push(tabID);
                    this.$store.commit('tab/pushHistory', {
                        routeName: this.$route.name,
                        tabID    : tabID,
                        tabKey   : window.history.state.key
                    });
                }
            },

            /****************************************************************************************************
             * METHOD: ADD IMAGE
             * Add general image upload card
             */
            addImage() {
                let maxImageID = -1;
                for(let i=0; i< this.request.images.length; i++) {
                    let imageID = parseInt(this.request.images[i].id);
                    if(imageID >= maxImageID)
                        maxImageID = imageID;
                }

                this.request.images.push({
                    id   : maxImageID + 1,
                    image: null
                });
            },

            /****************************************************************************************************
             * METHOD: ADD ITEM
             * Add variation card
             */
            addItem(variationIndex) {
                // get unique item id
                let maxItemID = -1;
                for(let i=0; i<this.request.variations[variationIndex].items.length; i++) {
                    let itemID = parseInt(this.request.variations[variationIndex].items[i].id);
                    if(itemID >= maxItemID)
                        maxItemID = itemID;
                }
                this.request.variations[variationIndex].items.push({
                    id     : maxItemID + 1,
                    label  : '',
                    image  : null,
                });
            },

            /****************************************************************************************************
             * METHOD: SHOW UPLOAD CROPPER DIALOG
             * Show dialog to handle image uploads
             */
            showUploadCropperDialog(itemIndex, variantIndex) {
                // inside general tab
                if(this.$route.params.tab === 'general') {
                    this.config.activePhoto = itemIndex;
                }
                // inside variations tab
                else if(this.$route.params.tab === 'variations') {
                    this.config.activeVariation = variantIndex;
                    this.config.variations[variantIndex].activeIndex = itemIndex;
                }
                this.$store.commit('dialog/cropper/show');
            },

            /****************************************************************************************************
             * METHOD: UPLOAD PHOTO
             * Handle emitted image upload event
             */
            uploadPhoto(formData) {
                if(this.$route.params.tab === 'general') {
                    if(this.config.activePhoto > -1) {
                        api_product.uploadPhoto(formData).then(response => {
                            if(!response) return;

                            this.request.images[this.config.activePhoto]['image'] = response.data.file_name;
                            this.$store.commit('dialog/cropper/hide');
                            setTimeout(() => {
                                if(this.request.images[this.request.images.length-1]['image'] == null) {
                                    this.request.images.pop();
                                    setTimeout(() => {
                                        this.addImage();
                                    }, 1);
                                }
                                else
                                    this.addImage();
                            }, 1);
                        }).catch(errors => {
                            this.$store.commit('dialog/cropper/load', false);
                            this.$store.commit('dialog/error/show', errors);
                            this.response.message = errors.response.data.message;
                            this.response.errors  = errors.response.data.errors;
                        });
                    }
                }
                else if(this.$route.params.tab === 'variations') {
                    if(this.config.activeVariation > -1) {
                        let activeVariation = this.config.activeVariation;
                        if (activeVariation > -1) {
                            let activeVariationItem = this.config.variations[activeVariation].activeIndex;
                            if (activeVariationItem > -1) {
                                api_product.uploadPhoto(formData).then(response => {
                                    if(!response) return;

                                    this.request.variations[activeVariation].items[activeVariationItem].image = response.data.file_name;
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
            },

            /****************************************************************************************************
             * METHOD: SHOW DELETE CONFIRM DIALOG
             * Show dialog to confirm delete of variation item
             */
            showDeleteConfirmDialog(itemIndex, variantIndex) {
                // inside general tab
                if(this.$route.params.tab === 'general') {
                    this.config.activePhoto = itemIndex;
                    if(this.request.images[itemIndex] == null)
                        this.request.images.splice(itemIndex, 1);
                    else {
                        this.$store.commit('dialog/confirm/show', {
                            title   : 'Delete Photo',
                            prompt  : 'Do you really want to delete this photo?',
                            noIcon  : 'arrow_back',
                            noLabel : 'BACK',
                            yesIcon : 'delete',
                            yesLabel: 'DELETE',
                            yesClass: 'error',
                            yesCallback: {
                                action: () => {
                                    this.request.images.splice(itemIndex, 1);
                                }
                            }
                        });
                    }
                }
                // inside variations tab
                else if(this.$route.params.tab === 'variations') {
                    this.config.activeVariation = variantIndex;
                    this.config.variations[variantIndex].activeIndex = itemIndex;

                    let item  = this.request.variations[variantIndex].items[itemIndex];
                    if(item.image == null && item.label === '') {
                        this.request.variations[variantIndex].items.splice(itemIndex, 1);
                        this.deleteStockPrice(itemIndex, variantIndex);
                    }
                    else {
                        let title = this.request.variations[variantIndex].title.toUpperCase().trim();
                        this.$store.commit('dialog/confirm/show', {
                            title   : 'Delete' + (title !== '' ? ' ' + title.toUpperCase() : ''),
                            prompt  : 'Do you really want to delete ' + (item.label !== '' ? 'the following' : 'this') + ' ' + (title !== '' ? title : 'variation item') + '?<div class="mt-5"><small class="text-body-1 primary--text">' + item.label.toUpperCase() + '</small></div>',
                            noIcon  : 'arrow_back',
                            noLabel : 'BACK',
                            yesIcon : 'delete',
                            yesLabel: 'DELETE',
                            yesClass: 'error',
                            yesCallback: {
                                action: () => {
                                    this.request.variations[variantIndex].items.splice(itemIndex, 1);
                                    this.deleteStockPrice(itemIndex, variantIndex);
                                }
                            }
                        });
                    }
                }
            },

            /****************************************************************************************************
             * METHOD: DELETE STOCK PRICE
             * Delete specific price and stock
             */
            deleteStockPrice(itemIndex, variantIndex) {
                // handle var1_only...
                if(variantIndex === 0) {
                    delete this.request.price.var1_only[`${itemIndex}`];
                    delete this.request.stock.var1_only[`${itemIndex}`];

                    // reposition var1_only values
                    for(let i=(itemIndex + 1); i <= this.request.variations[0].items.length; i++) {
                        // move price value
                        if(this.request.price.var1_only[`${i}`]) {
                            this.request.price.var1_only[`${i-1}`] = this.request.price.var1_only[`${i}`];
                            delete this.request.price.var1_only[`${i}`];
                        }
                        // move stock value
                        if(this.request.stock.var1_only[`${i}`]) {
                            this.request.stock.var1_only[`${i-1}`] = this.request.stock.var1_only[`${i}`];
                            delete this.request.stock.var1_only[`${i}`];
                        }
                    }
                }

                // handle var2_only...
                else if(variantIndex === 1) {
                    delete this.request.price.var2_only[`${itemIndex}`];
                    delete this.request.stock.var2_only[`${itemIndex}`];

                    // reposition var2_only values
                    for(let i=(itemIndex + 1); i <= this.request.variations[1].items.length; i++) {
                        // move price value
                        if(this.request.price.var2_only[`${i}`]) {
                            this.request.price.var2_only[`${i-1}`] = this.request.price.var2_only[`${i}`];
                            delete this.request.price.var2_only[`${i}`];
                        }
                        // move stock value
                        if(this.request.stock.var2_only[`${i}`]) {
                            this.request.stock.var2_only[`${i-1}`] = this.request.stock.var2_only[`${i}`];
                            delete this.request.stock.var2_only[`${i}`];
                        }
                    }
                }

                // handle both_vars...
                // delete both_vars prices
                let bothVarPriceKeys = Object.keys(this.request.price.both_vars);
                for(let i=0; i<bothVarPriceKeys.length; i++) {
                    let keys = bothVarPriceKeys[i].split('_');

                    if(variantIndex === 0) {
                        if(parseInt(keys[0]) === itemIndex)
                            delete this.request.price.both_vars[bothVarPriceKeys[i]];
                    }
                    else if(variantIndex === 1) {
                        if(parseInt(keys[1]) === itemIndex)
                            delete this.request.price.both_vars[bothVarPriceKeys[i]];
                    }
                }

                // reposition both_vars prices
                for(let i = 0; i < bothVarPriceKeys.length; i++) {
                    let keys = bothVarPriceKeys[i].split('_');
                    let key1 = parseInt(keys[0]);
                    let key2 = parseInt(keys[1]);

                    if(variantIndex === 0) {
                        if(key1 > itemIndex) {
                            this.request.price.both_vars[`${key1-1}_${key2}`] = this.request.price.both_vars[bothVarPriceKeys[i]];
                            delete this.request.price.both_vars[bothVarPriceKeys[i]];
                        }
                    }
                    if(variantIndex === 1) {
                        if(key2 > itemIndex) {
                            this.request.price.both_vars[`${key1}_${key2-1}`] = this.request.price.both_vars[bothVarPriceKeys[i]];
                            delete this.request.price.both_vars[bothVarPriceKeys[i]];
                        }
                    }
                }

                // delete both_vars stocks
                let bothVarStockKeys = Object.keys(this.request.stock.both_vars);
                for(let i=0; i<bothVarStockKeys.length; i++) {
                    let keys = bothVarStockKeys[i].split('_');

                    if(variantIndex === 0) {
                        if(parseInt(keys[0]) === itemIndex)
                            delete this.request.stock.both_vars[bothVarStockKeys[i]];
                    }
                    else if(variantIndex === 1) {
                        if(parseInt(keys[1]) === itemIndex)
                            delete this.request.stock.both_vars[bothVarStockKeys[i]];
                    }
                }

                // reposition both_vars stocks
                for(let i = 0; i < bothVarStockKeys.length; i++) {
                    let keys = bothVarStockKeys[i].split('_');
                    let key1 = parseInt(keys[0]);
                    let key2 = parseInt(keys[1]);

                    if(variantIndex === 0) {
                        if(key1 > itemIndex) {
                            this.request.stock.both_vars[`${key1-1}_${key2}`] = this.request.stock.both_vars[bothVarStockKeys[i]];
                            delete this.request.stock.both_vars[bothVarStockKeys[i]];
                        }
                    }
                    else if(variantIndex === 1) {
                        if(key2 > itemIndex) {
                            this.request.stock.both_vars[`${key1}_${key2-1}`] = this.request.stock.both_vars[bothVarStockKeys[i]];
                            delete this.request.stock.both_vars[bothVarStockKeys[i]];
                        }
                    }
                }
            },

            /****************************************************************************************************
             * METHOD: VALIDATE FORMS
             * Validate entered data
             */
            validateForms() {
                let isValid = false;
                let generalForm    = this.$refs.generalForm;
                let variationsForm = this.$refs.variationsForm;
                let priceStockForm = this.$refs.priceStockForm;

                if(!this.config.btnAdd.loading) {
                    if(!generalForm.validate())
                        this.changeTab('general');
                    else if(this.request.variations[0].items.length <= 0 && this.request.variations[1].items.length <= 0) {
                        this.$store.commit('dialog/message/show', {
                            title: 'Notice',
                            prompt: 'Please add at least one product variation first.',
                            callback: {
                                action: () => {
                                    this.changeTab('variations');
                                }
                            }
                        });
                    }
                    else if(this.$route.name === this.config.addRoute) {
                        if(variationsForm == null)
                            this.changeTab('variations');
                        else if(!variationsForm.validate())
                            this.changeTab('variations');
                        else if(priceStockForm == null)
                            this.changeTab('price_stock');
                        else if(!priceStockForm.validate())
                            this.changeTab('price_stock');
                        else
                            isValid = true;
                    }
                    else if(this.$route.name === this.config.editRoute) {
                        isValid = true;
                        if(priceStockForm != null) {
                            if(!priceStockForm.validate()) {
                                this.changeTab('price_stock');
                                isValid = false;
                            }
                        }
                        if(variationsForm != null) {
                            if(!variationsForm.validate()) {
                                this.changeTab('variations');
                                isValid = false;
                            }
                        }
                    }
                }
                // append price_stock_mode to request
                this.request.price_stock_mode = this.variationMode;

                // return validation result
                return isValid;
            },

            /****************************************************************************************************
             * METHOD: STORE PRODUCT
             * Add inputted product
             */
            storeProduct() {
                if(this.validateForms()) {
                    this.config.btnAdd.loading = true;
                    api_product.store(this.request).then(response => {
                        if(!response) return;

                        if(response.data.stored) {
                            this.config.btnAdd.loading = false;
                            this.$store.commit('auth/data/purge', 'products');
                            this.goBack();
                        }
                    }).catch(errors => {
                        this.config.btnAdd.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                    })
                }
            },

            /****************************************************************************************************
             * METHOD: UPDATE PRODUCT
             * Update selected product
             */
            updateProduct() {
                if(this.validateForms()) {
                    this.config.btnUpdate.loading = true;
                    api_product.update(this.request, this.$route.params.product).then(response => {
                        if(!response) return;

                        if(response.data.updated) {
                            this.config.btnUpdate.loading = false;
                            this.$store.commit('auth/data/purge', 'products');
                            this.$store.commit('snackbar/bottom/show', {
                                prompt: 'Product updated!'
                            });

                            // cache
                            this.cache = JSON.stringify(this.request);
                            this.forceCache();
                        }
                    }).catch(errors => {
                        this.config.btnUpdate.loading = false;
                        this.$store.commit('dialog/error/show', errors);
                        this.response.message = errors.response.data.message;
                        this.response.errors  = errors.response.data.errors;
                    });
                }
            },

            /****************************************************************************************************
             * METHOD: ALLOW PICKUP CHANGE
             * Prompt about delivery method availability
             */
            allowPickupChange() {
                if(!this.request.allows_pickup) {
                    this.$store.commit('dialog/message/show', {
                        title : 'Notice',
                        prompt: 'The only <span class="primary--text">delivery method</span> available now is through <span class="primary--text">Pickup</span>.',
                        callback: {
                            action: () => {
                                this.request.allows_pickup = true;
                            }
                        }
                    });
                }
            }
        },
        mounted() {
            this.onLoad();
            setTimeout(() => {
                this.config.showActions = true;
                this.config.animation   = true;
            }, 300);
        }
    }
</script>

<style scoped>

</style>
