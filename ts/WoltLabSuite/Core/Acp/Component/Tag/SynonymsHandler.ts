/**
 * Handles the dialog to set tags as synonyms.
 *
 * @author  Olaf Braun
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */

import { add as addEvent } from "WoltLabSuite/Core/Event/Handler";
import { ClipboardActionData } from "WoltLabSuite/Core/Controller/Clipboard/Data";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { getPhrase } from "WoltLabSuite/Core/Language";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import { dboAction } from "WoltLabSuite/Core/Ajax";

export function init() {
  addEvent("com.woltlab.wcf.clipboard", "com.woltlab.wcf.tag", (actionData: { data: ClipboardActionData }) => {
    if (actionData.data.actionName === "com.woltlab.wcf.tag.setAsSynonyms") {
      openDialog(actionData.data.parameters.objectIDs, actionData.data.parameters.template);
    }
  });
}

function openDialog(objectIDs: number[], template: string) {
  const dialog = dialogFactory().fromHtml(template).asConfirmation();
  dialog.addEventListener("validate", (event) => {
    const checked = dialog.querySelectorAll("input[type=radio]:checked").length > 0;
    event.detail.push(Promise.resolve(checked));

    DomUtil.innerError(
      dialog.querySelector(".containerBoxList")!,
      checked ? undefined : getPhrase("wcf.global.form.error.empty"),
    );
  });
  dialog.addEventListener("primary", () => {
    void dboAction("setAsSynonyms", "wcf\\data\\tag\\TagAction")
      .objectIds(objectIDs)
      .payload({
        tagID: dialog.querySelector<HTMLInputElement>('input[name="tagID"]:checked')!.value,
      })
      .dispatch()
      .then(() => {
        window.location.reload();
      });
  });

  dialog.show(getPhrase("wcf.acp.tag.setAsSynonyms"));
}
