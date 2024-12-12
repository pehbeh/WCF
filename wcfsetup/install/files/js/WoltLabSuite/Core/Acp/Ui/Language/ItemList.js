/**
 * Handles language item list.
 *
 * @author  Olaf Braun
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
define(["require", "exports", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Component/Confirmation", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, Ajax_1, Dialog_1, Language_1, Confirmation_1, Notification_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.init = init;
    function init() {
        document.querySelectorAll(".jsLanguageItem").forEach((button) => {
            button.addEventListener("click", () => {
                void beginEdit(parseInt(button.dataset.languageItemId, 10));
            });
        });
    }
    async function beginEdit(languageItemID) {
        const result = (await (0, Ajax_1.dboAction)("prepareEdit", "wcf\\data\\language\\item\\LanguageItemAction")
            .objectIds([languageItemID])
            .dispatch());
        const dialog = (0, Dialog_1.dialogFactory)()
            .fromHtml(result.template)
            .asPrompt(result.isCustomLanguageItem
            ? {
                extra: (0, Language_1.getPhrase)("wcf.global.button.delete"),
            }
            : undefined);
        dialog.addEventListener("extra", () => {
            void (0, Confirmation_1.confirmationFactory)()
                .custom((0, Language_1.getPhrase)("wcf.global.confirmation.title"))
                .message((0, Language_1.getPhrase)("wcf.acp.language.item.delete.confirmMessage"))
                .then((result) => {
                if (result) {
                    void (0, Ajax_1.dboAction)("deleteCustomLanguageItems", "wcf\\data\\language\\item\\LanguageItemAction")
                        .objectIds([languageItemID])
                        .dispatch();
                    dialog.close();
                    window.location.reload();
                }
            });
        });
        dialog.addEventListener("primary", () => {
            const languageItemValue = dialog.querySelector('[name="languageItemValue"]')?.value;
            const languageCustomItemValue = dialog.querySelector('[name="languageCustomItemValue"]')?.value;
            const languageUseCustomValue = dialog.querySelector('[name="languageUseCustomValue"]')?.checked;
            void (0, Ajax_1.dboAction)("edit", "wcf\\data\\language\\item\\LanguageItemAction")
                .objectIds([languageItemID])
                .payload({
                languageItemValue: languageItemValue ?? null,
                languageCustomItemValue: languageCustomItemValue ?? null,
                languageUseCustomValue: languageUseCustomValue ?? null,
            })
                .dispatch()
                .then(() => {
                (0, Notification_1.show)();
            });
        });
        dialog.show(result.languageItem);
    }
});
