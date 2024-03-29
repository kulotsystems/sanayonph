// --- MODULES ------------------------------------------------------------
import loader  from './dialogs/store-dialog-loader.js'
import message from './dialogs/store-dialog-message.js';
import confirm from './dialogs/store-dialog-confirm.js';
import error   from './dialogs/store-dialog-error.js';
import image   from './dialogs/store-dialog-image.js';
import cropper from './dialogs/store-dialog-cropper.js'

export default {
    namespaced: true,

    modules: {
        loader,
        message,
        confirm,
        error,
        image,
        cropper
    }
}
