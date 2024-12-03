/**
 * Handles language item list.
 *
 * @author  Olaf Braun
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */

import { dboAction } from "WoltLabSuite/Core/Ajax";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";

interface BeginEditResponse {
  languageItem: string;
  isCustomLanguageItem: boolean;
  template: string;
}

export function init() {
  document.querySelectorAll<HTMLElement>(".jsLanguageItem").forEach((button) => {
    button.addEventListener("click", () => {
      void beginEdit(parseInt(button.dataset.languageItemId!, 10));
    });
  });
}

async function beginEdit(languageItemID: number) {
  const result = (await dboAction("prepareEdit", "wcf\\data\\language\\item\\LanguageItemAction")
    .objectIds([languageItemID])
    .dispatch()) as BeginEditResponse;

  const dialog = dialogFactory()
    .fromHtml(result.template)
    .asPrompt(
      result.isCustomLanguageItem
        ? {
            extra: getPhrase("wcf.global.button.delete"),
          }
        : undefined,
    );

  dialog.addEventListener("extra", () => {
    void confirmationFactory()
      .custom(getPhrase("wcf.global.confirmation.title"))
      .message(getPhrase("wcf.acp.language.item.delete.confirmMessage"))
      .then((result) => {
        if (result) {
          void dboAction("deleteCustomLanguageItems", "wcf\\data\\language\\item\\LanguageItemAction")
            .objectIds([languageItemID])
            .dispatch();

          dialog.close();

          window.location.reload();
        }
      });
  });

  dialog.addEventListener("primary", () => {
    const languageItemValue = dialog.querySelector<HTMLInputElement>('[name="languageItemValue"]')?.value;
    const languageCustomItemValue = dialog.querySelector<HTMLInputElement>('[name="languageCustomItemValue"]')?.value;
    const languageUseCustomValue = dialog.querySelector<HTMLInputElement>('[name="languageUseCustomValue"]')?.checked;

    void dboAction("edit", "wcf\\data\\language\\item\\LanguageItemAction")
      .objectIds([languageItemID])
      .payload({
        languageItemValue: languageItemValue ?? null,
        languageCustomItemValue: languageCustomItemValue ?? null,
        languageUseCustomValue: languageUseCustomValue ?? null,
      })
      .dispatch()
      .then(() => {
        showNotification();
      });
  });

  dialog.show(result.languageItem);
}
