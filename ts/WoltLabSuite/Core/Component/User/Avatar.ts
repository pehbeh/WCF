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

interface Result {
  avatar: string;
}

async function editAvatar(button: HTMLElement): Promise<void> {
  const { ok, result } = await dialogFactory().usingFormBuilder().fromEndpoint<Result>(button.dataset.editAvatar!);

  if (ok) {
    const avatarForm = document.getElementById("avatarForm");
    if (avatarForm) {
      // In the ACP, the form should not be reloaded after changing the avatar.
      avatarForm.querySelector<HTMLImageElement>("img.userAvatarImage")!.src = result.avatar;
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

  wheneverFirstSeen("[data-edit-avatar]", (button) => {
    button.addEventListener(
      "click",
      promiseMutex(() => editAvatar(button)),
    );
  });
}
