/**
 * Handles the buttons on the notifcation list page.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 * @woltlabExcludeBundle tiny
 */
define(["require", "exports", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Component/Confirmation", "WoltLabSuite/Core/Helper/PromiseMutex", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, Ajax_1, Confirmation_1, PromiseMutex_1, Language_1, Notification_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    function initMarkAllAsRead() {
        document.querySelector(".jsMarkAllAsConfirmed")?.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => markAllAsRead()));
    }
    async function markAllAsRead() {
        const result = await (0, Confirmation_1.confirmationFactory)()
            .custom((0, Language_1.getPhrase)("wcf.user.notification.markAllAsConfirmed.confirmMessage"))
            .withoutMessage();
        if (!result) {
            return;
        }
        await (0, Ajax_1.dboAction)("markAllAsConfirmed", "wcf\\data\\user\\notification\\UserNotificationAction").dispatch();
        (0, Notification_1.show)(undefined, () => {
            window.location.reload();
        });
    }
    function initMarkAsRead() {
        document.querySelectorAll('.notificationListItem[data-is-read="false"]').forEach((element) => {
            element.querySelector(".notificationListItem__markAsRead")?.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => markAsRead(element)));
        });
    }
    async function markAsRead(element) {
        await (0, Ajax_1.dboAction)("markAsConfirmed", "wcf\\data\\user\\notification\\UserNotificationAction")
            .objectIds([parseInt(element.dataset.objectId)])
            .dispatch();
        element.querySelector(".notificationListItem__unread")?.remove();
        element.dataset.isRead = "true";
    }
    function setup() {
        initMarkAllAsRead();
        initMarkAsRead();
    }
});
