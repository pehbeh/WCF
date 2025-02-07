/**
 * Handles bulk interactions that open a form builder dialog.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
define(["require", "exports", "WoltLabSuite/Core/Ui/Notification", "WoltLabSuite/Core/Component/Dialog"], function (require, exports, Notification_1, Dialog_1) {
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
        // TODO: This shows a generic success message and should be replaced with a more specific message.
        (0, Notification_1.show)();
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
