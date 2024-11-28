/**
 * Handles the button on the moderation activation page.
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @woltlabExcludeBundle tiny
 * @since 6.2
 */
define(["require", "exports", "WoltLabSuite/Core/Api/ModerationQueues/DeleteContent", "WoltLabSuite/Core/Api/ModerationQueues/EnableContent", "WoltLabSuite/Core/Component/Confirmation", "WoltLabSuite/Core/Helper/PromiseMutex", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, DeleteContent_1, EnableContent_1, Confirmation_1, PromiseMutex_1, Language_1, Notification_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    async function handleEnableContent(queueId, redirectUrl) {
        const result = await (0, Confirmation_1.confirmationFactory)()
            .custom((0, Language_1.getPhrase)("wcf.moderation.activation.enableContent.confirmMessage"))
            .withoutMessage();
        if (result) {
            const response = await (0, EnableContent_1.enableContent)(queueId);
            if (response.ok) {
                (0, Notification_1.show)(undefined, () => {
                    window.location.href = redirectUrl;
                });
            }
        }
    }
    async function handleRemoveContent(queueId, objectName, redirectUrl) {
        const { result, reason } = await (0, Confirmation_1.confirmationFactory)().softDelete(objectName, true);
        if (result) {
            const response = await (0, DeleteContent_1.deleteContent)(queueId, reason);
            if (response.ok) {
                (0, Notification_1.show)(undefined, () => {
                    window.location.href = redirectUrl;
                });
            }
        }
    }
    function setup(enableContentButton, removeContentButton) {
        enableContentButton.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => handleEnableContent(parseInt(removeContentButton.dataset.objectId), removeContentButton.dataset.redirectUrl)));
        removeContentButton.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => handleRemoveContent(parseInt(removeContentButton.dataset.objectId), removeContentButton.dataset.objectName, removeContentButton.dataset.redirectUrl)));
    }
});
