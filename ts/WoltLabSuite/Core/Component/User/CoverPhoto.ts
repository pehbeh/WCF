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

type ResponseGetForm = {
  dialog: string;
  formId: string;
  title: string;
};

async function editCoverPhoto(button: HTMLElement, defaultCoverPhoto?: string): Promise<void> {
  const json = (await prepareRequest(button.dataset.editCoverPhoto!).get().fetchAsJson()) as ResponseGetForm;
  const dialog = dialogFactory().fromHtml(json.dialog).withoutControls();
  const oldCoverPhoto = document.querySelector<HTMLElement>(".userProfileCoverPhoto")?.style.backgroundImage;

  dialog.addEventListener("afterClose", () => {
    const file = dialog.querySelector<WoltlabCoreFile>("woltlab-core-file");
    const coverPhotoUrl = file?.link ?? defaultCoverPhoto;

    if (FormBuilderManager.hasForm(json.formId)) {
      FormBuilderManager.unregisterForm(json.formId);
    }

    if (oldCoverPhoto === `url("${coverPhotoUrl}")`) {
      // nothing changed
      return;
    }

    const photo = document.querySelector<HTMLElement>(".userProfileCoverPhoto");
    photo!.style.setProperty("background-image", `url(${coverPhotoUrl})`, "");
    showNotification();
    fireEvent("com.woltlab.wcf.user", "coverPhoto", {
      url: coverPhotoUrl,
    });
  });

  dialog.show(json.title);
}

export function setup(defaultCoverPhoto?: string): void {
  wheneverFirstSeen("[data-edit-cover-photo]", (button) => {
    button.addEventListener(
      "click",
      promiseMutex(() => editCoverPhoto(button, defaultCoverPhoto)),
    );
  });
}
