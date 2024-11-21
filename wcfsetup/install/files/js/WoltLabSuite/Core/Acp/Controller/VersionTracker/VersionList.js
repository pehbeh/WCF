/**
 * Handles the list of versions in the version tracker list.
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Api/Versionstrackers/RevertVersion", "WoltLabSuite/Core/Component/Confirmation", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, RevertVersion_1, Confirmation_1, UiNotification) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    UiNotification = tslib_1.__importStar(UiNotification);
    function initRevertButtons(container, objectType, objectId) {
        container.querySelectorAll(".jsRevertButton").forEach((button) => {
            button.addEventListener("click", async () => {
                const result = await (0, Confirmation_1.confirmationFactory)().custom(button.dataset.confirmMessage).withoutMessage();
                if (!result) {
                    return;
                }
                const response = await (0, RevertVersion_1.revertVersion)(objectType, objectId, parseInt(button.dataset.objectId));
                if (response.ok) {
                    UiNotification.show(undefined, () => {
                        window.location.reload();
                    });
                }
            });
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
            if (newIdInput.checked) {
                newInputChanged(newIdInput);
            }
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
            if (oldIdInput.checked) {
                oldInputChanged(oldIdInput);
            }
        });
    }
    function setup(container, objectType, objectId) {
        initRevertButtons(container, objectType, objectId);
        initRadioButtons(container);
    }
});
