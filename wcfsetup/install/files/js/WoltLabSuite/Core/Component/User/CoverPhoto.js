/**
 * Handles the user cover photo edit buttons.
 *
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 * @woltlabExcludeBundle all
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Helper/PromiseMutex", "WoltLabSuite/Core/Helper/Selector", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Ajax/Backend", "WoltLabSuite/Core/Ui/Notification", "WoltLabSuite/Core/Form/Builder/Manager", "WoltLabSuite/Core/Event/Handler"], function (require, exports, tslib_1, PromiseMutex_1, Selector_1, Dialog_1, Backend_1, Notification_1, FormBuilderManager, Handler_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    FormBuilderManager = tslib_1.__importStar(FormBuilderManager);
    async function editCoverPhoto(button, defaultCoverPhoto) {
        const json = (await (0, Backend_1.prepareRequest)(button.dataset.editCoverPhoto).get().fetchAsJson());
        const dialog = (0, Dialog_1.dialogFactory)().fromHtml(json.dialog).withoutControls();
        const oldCoverPhoto = document.querySelector(".userProfileCoverPhoto")?.style.backgroundImage;
        dialog.addEventListener("afterClose", () => {
            const file = dialog.querySelector("woltlab-core-file");
            const coverPhotoUrl = file?.link ?? defaultCoverPhoto;
            if (FormBuilderManager.hasForm(json.formId)) {
                FormBuilderManager.unregisterForm(json.formId);
            }
            if (oldCoverPhoto === `url("${coverPhotoUrl}")`) {
                // nothing changed
                return;
            }
            const photo = document.querySelector(".userProfileCoverPhoto");
            photo.style.setProperty("background-image", `url(${coverPhotoUrl})`, "");
            (0, Notification_1.show)();
            (0, Handler_1.fire)("com.woltlab.wcf.user", "coverPhoto", {
                url: coverPhotoUrl,
            });
        });
        dialog.show(json.title);
    }
    function setup(defaultCoverPhoto) {
        (0, Selector_1.wheneverFirstSeen)("[data-edit-cover-photo]", (button) => {
            button.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => editCoverPhoto(button, defaultCoverPhoto)));
        });
    }
});
