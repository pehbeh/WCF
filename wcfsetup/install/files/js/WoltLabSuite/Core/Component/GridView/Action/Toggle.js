define(["require", "exports", "WoltLabSuite/Core/Helper/Selector", "WoltLabSuite/Core/Api/PostObject"], function (require, exports, Selector_1, PostObject_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    async function handleToggle(checked, enableEndpoint, disableEndpoint) {
        await (0, PostObject_1.postObject)(checked ? enableEndpoint : disableEndpoint);
    }
    function setup(tableId) {
        (0, Selector_1.wheneverFirstSeen)(`#${tableId} .gridView__row woltlab-core-toggle-button`, (toggleButton) => {
            toggleButton.addEventListener("change", (event) => {
                void handleToggle(event.detail.checked, toggleButton.dataset.enableEndpoint, toggleButton.dataset.disableEndpoint);
            });
        });
    }
});
