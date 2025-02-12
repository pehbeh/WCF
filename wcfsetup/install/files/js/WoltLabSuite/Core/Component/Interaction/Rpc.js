/**
 * Handles interactions that call a RPC endpoint.
 *
 * @author Marcel Werk
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
define(["require", "exports", "WoltLabSuite/Core/Api/DeleteObject", "WoltLabSuite/Core/Api/PostObject", "./Confirmation", "WoltLabSuite/Core/Component/Snackbar", "WoltLabSuite/Core/Language"], function (require, exports, DeleteObject_1, PostObject_1, Confirmation_1, Snackbar_1, Language_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    async function handleRpcInteraction(container, element, objectName, endpoint, confirmationType, customConfirmationMessage = "", invalidatesAllItems = false) {
        const confirmationResult = await (0, Confirmation_1.handleConfirmation)(objectName, confirmationType, customConfirmationMessage);
        if (!confirmationResult.result) {
            return;
        }
        if (confirmationType == Confirmation_1.ConfirmationType.Delete) {
            const result = await (0, DeleteObject_1.deleteObject)(endpoint);
            if (!result.ok) {
                return;
            }
        }
        else {
            const result = await (0, PostObject_1.postObject)(endpoint, confirmationResult.reason ? { reason: confirmationResult.reason } : undefined);
            if (!result.ok) {
                return;
            }
        }
        if (confirmationType === Confirmation_1.ConfirmationType.Delete) {
            element.dispatchEvent(new CustomEvent("interaction:remove", {
                bubbles: true,
            }));
            (0, Snackbar_1.showSuccessSnackbar)((0, Language_1.getPhrase)("wcf.global.success.delete"));
        }
        else {
            if (invalidatesAllItems) {
                container.dispatchEvent(new CustomEvent("interaction:invalidate-all"));
            }
            else {
                element.dispatchEvent(new CustomEvent("interaction:invalidate", {
                    bubbles: true,
                }));
            }
            (0, Snackbar_1.showDefaultSuccessSnackbar)();
        }
    }
    function setup(identifier, container) {
        container.addEventListener("interaction:execute", (event) => {
            if (event.detail.interaction === identifier) {
                void handleRpcInteraction(container, event.target, event.detail.objectName, event.detail.endpoint, event.detail.confirmationType, event.detail.confirmationMessage, event.detail.invalidatesAllItems === "true");
            }
        });
    }
});
