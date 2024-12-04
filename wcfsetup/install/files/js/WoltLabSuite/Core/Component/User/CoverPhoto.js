/**
 * Handles the user cover photo edit buttons.
 *
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 * @woltlabExcludeBundle all
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Helper/PromiseMutex", "WoltLabSuite/Core/Helper/Selector", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Ajax/Backend", "WoltLabSuite/Core/Ui/Notification", "WoltLabSuite/Core/Form/Builder/Manager", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/StringUtil"], function (require, exports, tslib_1, PromiseMutex_1, Selector_1, Dialog_1, Backend_1, Notification_1, FormBuilderManager, Handler_1, Language_1, Util_1, StringUtil_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    FormBuilderManager = tslib_1.__importStar(FormBuilderManager);
    Util_1 = tslib_1.__importDefault(Util_1);
    async function editCoverPhoto(button) {
        const defaultCoverPhoto = button.dataset.defaultCoverPhoto;
        const json = (await (0, Backend_1.prepareRequest)(button.dataset.editCoverPhoto).get().fetchAsJson());
        const dialog = (0, Dialog_1.dialogFactory)().fromHtml(json.dialog).withoutControls();
        const coverPhotoElement = getCoverPhotoElement();
        const coverPhotoNotice = document.getElementById("coverPhotoNotice");
        const oldCoverPhoto = coverPhotoElement?.style.backgroundImage;
        dialog.addEventListener("afterClose", () => {
            const file = dialog.querySelector("woltlab-core-file");
            const coverPhotoUrl = (0, StringUtil_1.unescapeHTML)(file?.link ?? defaultCoverPhoto ?? "");
            const coverPhotoStyle = `url("${coverPhotoUrl}")`;
            if (FormBuilderManager.hasForm(json.formId)) {
                FormBuilderManager.unregisterForm(json.formId);
            }
            if (oldCoverPhoto === coverPhotoUrl || oldCoverPhoto === coverPhotoStyle) {
                // nothing changed
                return;
            }
            if (coverPhotoElement && coverPhotoUrl) {
                coverPhotoElement.style.setProperty("background-image", coverPhotoStyle, "");
            }
            else {
                // ACP cover photo management
                if (!coverPhotoElement && coverPhotoUrl) {
                    coverPhotoNotice.parentElement.appendChild(Util_1.default.createFragmentFromHtml(`<div id="coverPhotoPreview" style="background-image: ${coverPhotoStyle};"></div>`));
                    coverPhotoNotice.remove();
                }
                else if (coverPhotoElement && !coverPhotoUrl) {
                    coverPhotoElement.parentElement.appendChild(Util_1.default.createFragmentFromHtml(`<woltlab-core-notice id="coverPhotoNotice" type="info">${(0, Language_1.getPhrase)("wcf.user.coverPhoto.noImage")}</woltlab-core-notice>`));
                    coverPhotoElement.remove();
                }
            }
            (0, Notification_1.show)();
            (0, Handler_1.fire)("com.woltlab.wcf.user", "coverPhoto", {
                url: coverPhotoUrl,
            });
        });
        dialog.show(json.title);
    }
    function getCoverPhotoElement() {
        return document.querySelector(".userProfileCoverPhoto") ?? document.getElementById("coverPhotoPreview");
    }
    function setup() {
        (0, Selector_1.wheneverFirstSeen)("[data-edit-cover-photo]", (button) => {
            button.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => editCoverPhoto(button)));
        });
    }
});
