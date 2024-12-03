/**
 * Handles the dialog to set tags as synonyms.
 *
 * @author  Olaf Braun
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Ajax"], function (require, exports, tslib_1, Handler_1, Dialog_1, Language_1, Util_1, Ajax_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.init = init;
    Util_1 = tslib_1.__importDefault(Util_1);
    function init() {
        (0, Handler_1.add)("com.woltlab.wcf.clipboard", "com.woltlab.wcf.tag", (actionData) => {
            if (actionData.data.actionName === "com.woltlab.wcf.tag.setAsSynonyms") {
                openDialog(actionData.data.parameters.objectIDs, actionData.data.parameters.template);
            }
        });
    }
    function openDialog(objectIDs, template) {
        const dialog = (0, Dialog_1.dialogFactory)().fromHtml(template).asConfirmation();
        dialog.addEventListener("validate", (event) => {
            const checked = dialog.querySelectorAll("input[type=radio]:checked").length > 0;
            event.detail.push(Promise.resolve(checked));
            Util_1.default.innerError(dialog.querySelector(".containerBoxList"), checked ? undefined : (0, Language_1.getPhrase)("wcf.global.form.error.empty"));
        });
        dialog.addEventListener("primary", () => {
            void (0, Ajax_1.dboAction)("setAsSynonyms", "wcf\\data\\tag\\TagAction")
                .objectIds(objectIDs)
                .payload({
                tagID: dialog.querySelector('input[name="tagID"]:checked').value,
            })
                .dispatch()
                .then(() => {
                window.location.reload();
            });
        });
        dialog.show((0, Language_1.getPhrase)("wcf.acp.tag.setAsSynonyms"));
    }
});
