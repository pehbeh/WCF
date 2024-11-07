/**
 * Handles the user avatar edit buttons.
 *
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 */

import { promiseMutex } from "WoltLabSuite/Core/Helper/PromiseMutex";
import { wheneverFirstSeen } from "WoltLabSuite/Core/Helper/Selector";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import UiCloseOverlay from "WoltLabSuite/Core/Ui/CloseOverlay";

async function editAvatar(button: HTMLElement): Promise<void> {
  // If the user is editing their own avatar, the control panel is open and can overlay the dialog.
  UiCloseOverlay.execute();

  const { ok } = await dialogFactory().usingFormBuilder().fromEndpoint(button.dataset.editAvatar!);

  if (ok) {
    // TODO can we simple replace all avatar images?
    window.location.reload();
  }
}

export function setup(): void {
  wheneverFirstSeen("[data-edit-avatar]", (button) => {
    button.addEventListener(
      "click",
      promiseMutex(() => editAvatar(button)),
    );
  });
}
