/**
 * Provides a dialog to copy an existing template group.
 *
 * @author  Olaf Braun, Alexander Ebert
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */

import * as UiNotification from "../../../../Ui/Notification";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";

interface Response {
  redirectURL: string;
}

export function init(): void {
  const button = document.querySelector(".jsButtonCopy") as HTMLAnchorElement;
  button.addEventListener("click", () => void click(button));
}

async function click(button: HTMLAnchorElement): Promise<void> {
  const result = await dialogFactory().usingFormBuilder().fromEndpoint<Response>(button.dataset.endpoint!);
  if (result.ok) {
    UiNotification.show(undefined, () => {
      window.location.href = result.result.redirectURL;
    });
  }
}

