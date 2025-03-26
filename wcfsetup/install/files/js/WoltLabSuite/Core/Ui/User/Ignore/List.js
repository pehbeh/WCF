/**
 * Shows the ignore dialogs when editing users on the page listing ignored users.
 *
 * @author  Matthias Schmidt
 * @copyright  2001-2021 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @woltlabExcludeBundle all
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Component/Snackbar", "../../../Form/Builder/Dialog", "../../../Language"], function (require, exports, tslib_1, Snackbar_1, Dialog_1, Language) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.UiUserIgnoreList = void 0;
    Dialog_1 = tslib_1.__importDefault(Dialog_1);
    Language = tslib_1.__importStar(Language);
    class UiUserIgnoreList {
        dialogs = new Map();
        constructor() {
            document
                .querySelectorAll(".jsEditIgnoreButton")
                .forEach((el) => el.addEventListener("click", (ev) => this.openDialog(ev)));
        }
        openDialog(event) {
            const button = event.currentTarget;
            const userId = ~~button.closest(".jsIgnoredUser").dataset.objectId;
            if (!this.dialogs.has(userId)) {
                this.dialogs.set(userId, new Dialog_1.default("ignoreDialog", "wcf\\data\\user\\ignore\\UserIgnoreAction", "getDialog", {
                    dialog: {
                        title: Language.get("wcf.user.button.ignore"),
                    },
                    actionParameters: {
                        userID: userId,
                    },
                    submitActionName: "submitDialog",
                    successCallback(data) {
                        (0, Snackbar_1.showDefaultSuccessSnackbar)().addEventListener("snackbar:close", () => {
                            if (!data.isIgnoredUser) {
                                window.location.reload();
                            }
                        });
                    },
                }));
            }
            this.dialogs.get(userId).open();
        }
    }
    exports.UiUserIgnoreList = UiUserIgnoreList;
    exports.default = UiUserIgnoreList;
});
