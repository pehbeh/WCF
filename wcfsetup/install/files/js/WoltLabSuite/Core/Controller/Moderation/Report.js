/**
 * Handles the button on the moderation report page.
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @woltlabExcludeBundle tiny
 * @since 6.2
 */
define(["require", "exports", "WoltLabSuite/Core/Api/ModerationQueues/ChangeJustifiedStatus", "WoltLabSuite/Core/Api/ModerationQueues/CloseReport", "WoltLabSuite/Core/Api/ModerationQueues/DeleteContent", "WoltLabSuite/Core/Component/Confirmation", "WoltLabSuite/Core/Helper/PromiseMutex", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, ChangeJustifiedStatus_1, CloseReport_1, DeleteContent_1, Confirmation_1, PromiseMutex_1, Language_1, Notification_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
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
    async function handleCloseReport(queueId, redirectUrl) {
        const { result, dialog } = await (0, Confirmation_1.confirmationFactory)()
            .custom((0, Language_1.getPhrase)("wcf.moderation.report.removeReport.confirmMessage"))
            .withFormElements((dialog) => {
            const label = document.createElement("label");
            const input = document.createElement("input");
            input.type = "checkbox";
            const text = document.createTextNode((0, Language_1.getPhrase)("wcf.moderation.report.removeReport.markAsJustified"));
            dialog.content.append(label);
            label.append(input, " ", text);
        });
        if (result) {
            const response = await (0, CloseReport_1.closeReport)(queueId, dialog.content.querySelector("input").checked);
            if (response.ok) {
                (0, Notification_1.show)(undefined, () => {
                    window.location.href = redirectUrl;
                });
            }
        }
    }
    async function handleChangeJustifiedStatus(queueId, justified, redirectUrl) {
        const { result, dialog } = await (0, Confirmation_1.confirmationFactory)()
            .custom((0, Language_1.getPhrase)("wcf.moderation.report.changeJustifiedStatus.confirmMessage"))
            .withFormElements((dialog) => {
            const label = document.createElement("label");
            const input = document.createElement("input");
            input.type = "checkbox";
            input.checked = justified;
            const text = document.createTextNode((0, Language_1.getPhrase)("wcf.moderation.report.changeJustifiedStatus.markAsJustified"));
            dialog.content.append(label);
            label.append(input, " ", text);
        });
        if (result) {
            const response = await (0, ChangeJustifiedStatus_1.changeJustifiedStatus)(queueId, dialog.content.querySelector("input").checked);
            if (response.ok) {
                (0, Notification_1.show)(undefined, () => {
                    window.location.href = redirectUrl;
                });
            }
        }
    }
    function setup(removeContentButton, closeReportButton, changeJustifiedStatusButton) {
        if (removeContentButton) {
            removeContentButton.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => handleRemoveContent(parseInt(removeContentButton.dataset.objectId), removeContentButton.dataset.objectName, removeContentButton.dataset.redirectUrl)));
        }
        if (closeReportButton) {
            closeReportButton.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => handleCloseReport(parseInt(closeReportButton.dataset.objectId), closeReportButton.dataset.redirectUrl)));
        }
        if (changeJustifiedStatusButton) {
            changeJustifiedStatusButton.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => handleChangeJustifiedStatus(parseInt(changeJustifiedStatusButton.dataset.objectId), changeJustifiedStatusButton.dataset.justified === "true", changeJustifiedStatusButton.dataset.redirectUrl)));
        }
    }
});
