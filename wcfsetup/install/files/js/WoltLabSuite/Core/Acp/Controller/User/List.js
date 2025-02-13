/**
 * Handles the list of users.
 *
 * @author  Olaf Braun
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
define(["require", "exports", "tslib", "../../../Event/Handler", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Ui/Dropdown/Simple", "WoltLabSuite/Core/Acp/Ui/User/Action/Handler/Ban", "WoltLabSuite/Core/Acp/Ui/User/Action/Handler/SendNewPassword", "WoltLabSuite/Core/Controller/Clipboard", "WoltLabSuite/Core/Acp/Ui/User/Editor", "WoltLabSuite/Core/Acp/Ui/User/Content/Remove/Clipboard", "WoltLabSuite/Core/Component/Snackbar"], function (require, exports, tslib_1, EventHandler, Ajax_1, Simple_1, Ban_1, SendNewPassword_1, Clipboard_1, Editor_1, Clipboard_2, Snackbar_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    EventHandler = tslib_1.__importStar(EventHandler);
    Simple_1 = tslib_1.__importDefault(Simple_1);
    Ban_1 = tslib_1.__importDefault(Ban_1);
    SendNewPassword_1 = tslib_1.__importDefault(SendNewPassword_1);
    Editor_1 = tslib_1.__importDefault(Editor_1);
    function getUserElements(userIDs) {
        return Array.from(document.querySelectorAll(".jsUserRow")).filter((userRow) => userIDs.includes(parseInt(userRow.dataset.objectId)));
    }
    function getDropdownMenu(userRow) {
        const userId = ~~userRow.dataset.objectId;
        const dropdownId = `userListDropdown${userId}`;
        return Simple_1.default.getDropdownMenu(dropdownId);
    }
    function refresh(userIDs) {
        (0, Clipboard_1.unmark)("com.woltlab.wcf.user", userIDs);
        (0, Snackbar_1.showDefaultSuccessSnackbar)();
        EventHandler.fire("com.woltlab.wcf.acp.user", "refresh", {
            userIds: userIDs,
        });
    }
    async function enableUsers(userIDs) {
        await (0, Ajax_1.dboAction)("enable", "wcf\\data\\user\\UserAction").objectIds(userIDs).dispatch();
        getUserElements(userIDs).forEach((userRow) => {
            userRow.dataset.enabled = "true";
            const button = getDropdownMenu(userRow).querySelector(".jsEnable");
            button.textContent = button.dataset.disableMessage;
        });
        refresh(userIDs);
    }
    function banUsers(userIDs) {
        new Ban_1.default(userIDs).ban(() => {
            getUserElements(userIDs).forEach((userRow) => {
                userRow.dataset.banned = "true";
                const button = getDropdownMenu(userRow).querySelector(".jsBan");
                button.textContent = button.dataset.unbanMessage;
                refresh(userIDs);
            });
        });
    }
    function sendNewPasswords(userIDs) {
        new SendNewPassword_1.default(userIDs, () => {
            refresh(userIDs);
        }).send();
    }
    function setupUserClipboard(hasMarkedItems) {
        (0, Clipboard_1.setup)({
            pageClassName: "wcf\\acp\\page\\UserListPage",
            hasMarkedItems: hasMarkedItems,
        });
        EventHandler.add("com.woltlab.wcf.clipboard", "com.woltlab.wcf.user", (data) => {
            switch (data.data.actionName) {
                case "com.woltlab.wcf.user.enable":
                    void enableUsers(data.data.parameters.objectIDs);
                    break;
                case "com.woltlab.wcf.user.ban":
                    banUsers(data.data.parameters.objectIDs);
                    break;
                case "com.woltlab.wcf.user.sendNewPassword":
                    sendNewPasswords(data.data.parameters.objectIDs);
                    break;
            }
        });
        new Clipboard_2.AcpUserContentRemoveClipboard();
    }
    function setup(hasMarkedItems) {
        setupUserClipboard(hasMarkedItems);
        new Editor_1.default();
    }
});
