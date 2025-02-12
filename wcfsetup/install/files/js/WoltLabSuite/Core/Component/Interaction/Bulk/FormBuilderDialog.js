/**
 * Handles bulk interactions that open a form builder dialog.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
define(["require", "exports", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Component/Snackbar"], function (require, exports, Dialog_1, Snackbar_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    async function handleFormBuilderDialogAction(container, objectIds, endpoint) {
        const { ok } = await (0, Dialog_1.dialogFactory)().usingFormBuilder().fromEndpoint(endpoint);
        if (!ok) {
            return;
        }
        for (let i = 0; i < objectIds.length; i++) {
            const element = container.querySelector(`[data-object-id="${objectIds[i]}"]`);
            if (!element) {
                continue;
            }
            element.dispatchEvent(new CustomEvent("interaction:invalidate", {
                bubbles: true,
            }));
        }
        (0, Snackbar_1.showDefaultSuccessSnackbar)();
        container.dispatchEvent(new CustomEvent("interaction:reset-selection"));
    }
    function setup(identifier, container) {
        container.addEventListener("bulk-interaction", (event) => {
            if (event.detail.bulkInteraction === identifier) {
                void handleFormBuilderDialogAction(container, JSON.parse(event.detail.objectIds), event.detail.endpoint);
            }
        });
    }
});
