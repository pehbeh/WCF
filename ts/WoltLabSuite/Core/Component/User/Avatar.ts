/**
 * Handles the user avatar edit buttons.
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
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { registerCallback } from "WoltLabSuite/Core/Form/Builder/Field/Controller/FileProcessor";
import WoltlabCoreFile from "WoltLabSuite/Core/Component/File/woltlab-core-file";

interface Result {
  avatar: string;
}

async function editAvatar(button: HTMLElement): Promise<void> {
  const { ok, result } = await dialogFactory().usingFormBuilder().fromEndpoint<Result>(button.dataset.editAvatar!);

  if (ok) {
    const avatarForm = document.getElementById("avatarForm");
    if (avatarForm) {
      const img = avatarForm.querySelector<HTMLImageElement>("img.userAvatarImage")!;
      if (img.src === result.avatar) {
        return;
      }

      // In the ACP, the form should not be reloaded after changing the avatar.
      img.src = result.avatar;
      showNotification();
    } else {
      window.location.reload();
    }
  }
}

export function setup(): void {
  wheneverFirstSeen(
    "#wcf\\\\action\\\\UserAvatarAction_avatarFileIDContainer woltlab-core-file img",
    (img: HTMLImageElement) => {
      img.classList.add("userAvatarImage");
      img.parentElement!.classList.add("userAvatar");
    },
  );

  const avatarForm = document.getElementById("avatarForm");
  if (avatarForm) {
    registerCallback("wcf\\action\\UserAvatarAction_avatarFileID", (fileId: number | undefined) => {
      if (!fileId) {
        return;
      }

      const file = document.querySelector<WoltlabCoreFile>(
        `#wcf\\\\action\\\\UserAvatarAction_avatarFileIDContainer woltlab-core-file[file-id="${fileId}"]`,
      )!;

      avatarForm.querySelector<HTMLImageElement>("img.userAvatarImage")!.src = file.link!;
      showNotification();
    });
  }

  wheneverFirstSeen("[data-edit-avatar]", (button) => {
    button.addEventListener(
      "click",
      promiseMutex(() => editAvatar(button)),
    );
  });
}
