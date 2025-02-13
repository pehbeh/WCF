/**
 * Provides a dialog to copy an existing template group.
 *
 * @author  Olaf Braun, Alexander Ebert
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
define(["require", "exports", "WoltLabSuite/Core/Component/Snackbar", "WoltLabSuite/Core/Component/Dialog"], function (require, exports, Snackbar_1, Dialog_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.init = init;
    function init() {
        const button = document.querySelector(".jsButtonCopy");
        button.addEventListener("click", () => void click(button));
    }
    async function click(button) {
        const result = await (0, Dialog_1.dialogFactory)().usingFormBuilder().fromEndpoint(button.dataset.endpoint);
        if (result.ok) {
            (0, Snackbar_1.showDefaultSuccessSnackbar)().addEventListener("snackbar:close", () => {
                window.location.href = result.result.redirectURL;
            });
        }
    }
});
