/**
 * Provides previews for CKEditor 5 message fields.
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */

import { promiseMutex } from "WoltLabSuite/Core/Helper/PromiseMutex";
import { dboAction } from "WoltLabSuite/Core/Ajax";
import { listenToCkeditor } from "WoltLabSuite/Core/Component/Ckeditor/Event";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { getPhrase } from "WoltLabSuite/Core/Language";
import DomUtil from "WoltLabSuite/Core/Dom/Util";

type ResponseGetMessagePreview = {
  message: string;
  raw: string;
};

async function loadPreview(message: string, objectType: string, objectId: number): Promise<void> {
  const response = (await dboAction("getMessagePreview", "wcf\\data\\bbcode\\MessagePreviewAction")
    .payload({
      data: {
        message,
      },
      messageObjectType: objectType,
      messageObjectID: objectId,
    })
    .dispatch()) as ResponseGetMessagePreview;

  const dialog = dialogFactory()
    .fromHtml('<div class="htmlContent">' + response.message + "</div>")
    .withoutControls();
  dialog.show(getPhrase("wcf.global.preview"));
}

export function setup(messageFieldId: string, previewButtonId: string, objectType: string, objectId: number): void {
  listenToCkeditor(document.getElementById(messageFieldId)!).ready(({ ckeditor }) => {
    document.getElementById(previewButtonId)?.addEventListener(
      "click",
      promiseMutex(() => {
        if (ckeditor.getHtml() === "") {
          DomUtil.innerError(ckeditor.element, getPhrase("wcf.global.form.error.empty"));
          return Promise.resolve();
        } else {
          DomUtil.innerError(ckeditor.element, false);
        }

        return loadPreview(ckeditor.getHtml(), objectType, objectId);
      }),
    );
  });
}
