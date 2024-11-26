/**
 * Handles the list of versions in the edit history.
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Component/Confirmation", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, Ajax_1, Confirmation_1, UiNotification) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    UiNotification = tslib_1.__importStar(UiNotification);
    function initRevertButtons(container) {
        container.querySelectorAll(".jsRevertButton").forEach((button) => {
            button.addEventListener("click", async () => {
                const result = await (0, Confirmation_1.confirmationFactory)().custom(button.dataset.confirmMessage).withoutMessage();
                if (!result) {
                    return;
                }
                void revert(parseInt(button.dataset.objectId));
            });
        });
    }
    async function revert(objectId) {
        await (0, Ajax_1.dboAction)("revert", "wcf\\data\\edit\\history\\entry\\EditHistoryEntryAction").objectIds([objectId]).dispatch();
        UiNotification.show(undefined, () => {
            window.location.reload();
        });
    }
    function initRadioButtons(container) {
        const oldIdInputs = container.querySelectorAll("input[name=oldID]");
        const newIdInputs = container.querySelectorAll("input[name=newID]");
        function newInputChanged(newIdInput) {
            const newId = newIdInput.value === "current" ? Infinity : parseInt(newIdInput.value);
            oldIdInputs.forEach((oldIdInput) => {
                const oldId = oldIdInput.value === "current" ? Infinity : parseInt(oldIdInput.value);
                oldIdInput.disabled = oldId >= newId;
            });
        }
        newIdInputs.forEach((newIdInput) => {
            newIdInput.addEventListener("change", () => {
                newInputChanged(newIdInput);
            });
            newInputChanged(newIdInput);
        });
        function oldInputChanged(oldIdInput) {
            const oldId = oldIdInput.value === "current" ? Infinity : parseInt(oldIdInput.value);
            newIdInputs.forEach((newIdInput) => {
                const newId = newIdInput.value === "current" ? Infinity : parseInt(newIdInput.value);
                newIdInput.disabled = newId <= oldId;
            });
        }
        oldIdInputs.forEach((oldIdInput) => {
            oldIdInput.addEventListener("change", () => {
                oldInputChanged(oldIdInput);
            });
            oldInputChanged(oldIdInput);
        });
    }
    function setup(container) {
        initRevertButtons(container);
        initRadioButtons(container);
    }
});
