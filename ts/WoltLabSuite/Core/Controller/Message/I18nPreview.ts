/**
 * Provides previews for mulitple CKEditor 5 message fields.
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
import { CKEditor } from "WoltLabSuite/Core/Component/Ckeditor";

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

function getEditorMap(messageFieldIds: string[]): Map<string, CKEditor> {
  const map = new Map<string, CKEditor>();
  messageFieldIds.forEach((messageFieldId) => {
    listenToCkeditor(document.getElementById(messageFieldId)!).ready(({ ckeditor }) => {
      map.set(messageFieldId, ckeditor);
    });
  });

  return map;
}

function getActiveEditor(map: Map<string, CKEditor>): CKEditor | undefined {
  let activeEditor: CKEditor | undefined = undefined;
  map.forEach((editor) => {
    if (editor.isVisible()) {
      activeEditor = editor;
    }
  });

  return activeEditor;
}

export function setup(messageFieldIds: string[], previewButtonId: string, objectType: string, objectId: number): void {
  const map = getEditorMap(messageFieldIds);

  document.getElementById(previewButtonId)?.addEventListener(
    "click",
    promiseMutex(() => {
      const activeEditor = getActiveEditor(map);
      if (activeEditor === undefined) {
        return Promise.resolve();
      }

      if (activeEditor.getHtml() === "") {
        DomUtil.innerError(activeEditor.element, getPhrase("wcf.global.form.error.empty"));
        return Promise.resolve();
      } else {
        DomUtil.innerError(activeEditor.element, false);
      }

      return loadPreview(activeEditor.getHtml(), objectType, objectId);
    }),
  );
}
