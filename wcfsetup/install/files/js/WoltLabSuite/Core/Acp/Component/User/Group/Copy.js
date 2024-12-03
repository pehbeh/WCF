/**
 * Handles the dialog to copy a user group.
 *
 * @author  Olaf Braun
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
define(["require", "exports", "WoltLabSuite/Core/Component/Dialog"], function (require, exports, Dialog_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.init = init;
    function init() {
        const button = document.querySelector(".jsButtonUserGroupCopy");
        button?.addEventListener("click", () => {
            void (0, Dialog_1.dialogFactory)()
                .usingFormBuilder()
                .fromEndpoint(button.dataset.endpoint)
                .then((result) => {
                if (result.ok) {
                    window.location.href = result.result.redirectURL;
                }
            });
        });
    }
});
