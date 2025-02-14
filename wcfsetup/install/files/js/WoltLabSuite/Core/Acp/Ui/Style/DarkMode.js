/**
 * Allows the addition of a dark mode to an existing style.
 *
 * @author Alexander Ebert
 * @copyright 2001-2022 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @module WoltLabSuite/Core/Acp/Ui/Style/DarkMode
 * @since 6.0
 */
define(["require", "exports", "WoltLabSuite/Core/Component/Snackbar", "../../../Ajax/Backend", "../../../Component/Confirmation", "../../../Language"], function (require, exports, Snackbar_1, Backend_1, Confirmation_1, Language_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    async function promptConfirmation(endpoint, question) {
        const ok = await (0, Confirmation_1.confirmationFactory)().custom(question).message((0, Language_1.getPhrase)("wcf.dialog.confirmation.cannotBeUndone"));
        if (ok) {
            const response = await (0, Backend_1.prepareRequest)(endpoint).post().fetchAsResponse();
            if (response?.ok) {
                (0, Snackbar_1.showDefaultSuccessSnackbar)().addEventListener("snackbar:close", () => {
                    window.location.reload();
                });
            }
        }
    }
    function setupAddDarkMode() {
        const button = document.querySelector(".jsButtonAddDarkMode");
        button?.addEventListener("click", () => {
            void promptConfirmation(button.dataset.endpoint, button.dataset.question);
        });
    }
    function setup() {
        setupAddDarkMode();
    }
});
