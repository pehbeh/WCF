/**
 * Handles the user avatar edit buttons.
 *
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Helper/PromiseMutex", "WoltLabSuite/Core/Helper/Selector", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Ui/CloseOverlay"], function (require, exports, tslib_1, PromiseMutex_1, Selector_1, Dialog_1, CloseOverlay_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    CloseOverlay_1 = tslib_1.__importDefault(CloseOverlay_1);
    async function editAvatar(button) {
        // If the user is editing their own avatar, the control panel is open and can overlay the dialog.
        CloseOverlay_1.default.execute();
        const { ok } = await (0, Dialog_1.dialogFactory)().usingFormBuilder().fromEndpoint(button.dataset.editAvatar);
        if (ok) {
            // TODO can we simple replace all avatar images?
            window.location.reload();
        }
    }
    function setup() {
        (0, Selector_1.wheneverFirstSeen)("[data-edit-avatar]", (button) => {
            button.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => editAvatar(button)));
        });
    }
});
