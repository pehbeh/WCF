/**
 * Handles a user ban button.
 *
 * @author  Joshua Ruesweg
 * @copyright  2001-2021 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.5
 */
define(["require", "exports", "tslib", "../../../../Core", "./Abstract", "./Handler/Ban", "../../../../Event/Handler", "WoltLabSuite/Core/Component/Snackbar"], function (require, exports, tslib_1, Core, Abstract_1, Ban_1, EventHandler, Snackbar_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.BanAction = void 0;
    Core = tslib_1.__importStar(Core);
    Abstract_1 = tslib_1.__importDefault(Abstract_1);
    Ban_1 = tslib_1.__importDefault(Ban_1);
    EventHandler = tslib_1.__importStar(EventHandler);
    class BanAction extends Abstract_1.default {
        banHandler;
        constructor(button, userId, userDataElement) {
            super(button, userId, userDataElement);
            this.banHandler = new Ban_1.default([this.userId]);
            this.button.addEventListener("click", (event) => {
                event.preventDefault();
                const isBanned = Core.stringToBool(this.userDataElement.dataset.banned);
                if (isBanned) {
                    this.banHandler.unban(() => {
                        this.userDataElement.dataset.banned = "false";
                        this.button.textContent = this.button.dataset.banMessage;
                        (0, Snackbar_1.showDefaultSuccessSnackbar)();
                        EventHandler.fire("com.woltlab.wcf.acp.user", "refresh", {
                            userIds: [this.userId],
                        });
                    });
                }
                else {
                    this.banHandler.ban(() => {
                        this.userDataElement.dataset.banned = "true";
                        this.button.textContent = this.button.dataset.unbanMessage;
                        (0, Snackbar_1.showDefaultSuccessSnackbar)();
                        EventHandler.fire("com.woltlab.wcf.acp.user", "refresh", {
                            userIds: [this.userId],
                        });
                    });
                }
            });
        }
    }
    exports.BanAction = BanAction;
    exports.default = BanAction;
});
