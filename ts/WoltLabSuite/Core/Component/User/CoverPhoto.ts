/**
 * Handles the user cover photo edit buttons.
 *
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 * @woltlabExcludeBundle all
 */

import { promiseMutex } from "WoltLabSuite/Core/Helper/PromiseMutex";
import { wheneverFirstSeen } from "WoltLabSuite/Core/Helper/Selector";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import * as FormBuilderManager from "WoltLabSuite/Core/Form/Builder/Manager";
import WoltlabCoreFile from "WoltLabSuite/Core/Component/File/woltlab-core-file";
import { fire as fireEvent } from "WoltLabSuite/Core/Event/Handler";
import { getPhrase } from "WoltLabSuite/Core/Language";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import { escapeHTML } from "WoltLabSuite/Core/StringUtil";

type ResponseGetForm = {
  dialog: string;
  formId: string;
  title: string;
};

async function editCoverPhoto(button: HTMLElement): Promise<void> {
  const defaultCoverPhoto = button.dataset.defaultCoverPhoto;
  const json = (await prepareRequest(button.dataset.editCoverPhoto!).get().fetchAsJson()) as ResponseGetForm;
  const dialog = dialogFactory().fromHtml(json.dialog).withoutControls();
  const coverPhotoElement = getCoverPhotoElement();
  const coverPhotoNotice = document.getElementById("coverPhotoNotice");
  const oldCoverPhoto = coverPhotoElement?.style.backgroundImage;

  dialog.addEventListener("afterClose", () => {
    const file = dialog.querySelector<WoltlabCoreFile>("woltlab-core-file");
    const coverPhotoUrl = file?.link ?? defaultCoverPhoto ?? "";
    const coverPhotoStyle = `url("${coverPhotoUrl}")`;

    if (FormBuilderManager.hasForm(json.formId)) {
      FormBuilderManager.unregisterForm(json.formId);
    }

    if (oldCoverPhoto === coverPhotoUrl || oldCoverPhoto === coverPhotoStyle) {
      // nothing changed
      return;
    }

    if (coverPhotoElement && coverPhotoUrl) {
      coverPhotoElement.style.backgroundImage = coverPhotoStyle;
    } else {
      // ACP cover photo management
      if (!coverPhotoElement && coverPhotoUrl) {
        coverPhotoNotice!.parentElement!.appendChild(
          DomUtil.createFragmentFromHtml(
            `<div id="coverPhotoPreview" style="background-image: ${escapeHTML(coverPhotoStyle)};"></div>`,
          ),
        );
        coverPhotoNotice!.remove();
      } else if (coverPhotoElement && !coverPhotoUrl) {
        coverPhotoElement.parentElement!.appendChild(
          DomUtil.createFragmentFromHtml(
            `<woltlab-core-notice id="coverPhotoNotice" type="info">${getPhrase("wcf.user.coverPhoto.noImage")}</woltlab-core-notice>`,
          ),
        );
        coverPhotoElement.remove();
      }
    }

    showNotification();
    fireEvent("com.woltlab.wcf.user", "coverPhoto", {
      url: coverPhotoUrl,
    });
  });

  dialog.show(json.title);
}

function getCoverPhotoElement(): HTMLElement | null {
  return document.querySelector<HTMLElement>(".userProfileCoverPhoto") ?? document.getElementById("coverPhotoPreview");
}

export function setup(): void {
  wheneverFirstSeen("[data-edit-cover-photo]", (button) => {
    button.addEventListener(
      "click",
      promiseMutex(() => editCoverPhoto(button)),
    );
  });
}
