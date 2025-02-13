/**
 * Shows the dialog that allows the user to configure the dashboard boxes.
 *
 * @author Marcel Werk
 * @copyright 2001-2023 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.1
 */

import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { showDefaultSuccessSnackbar } from "WoltLabSuite/Core/Component/Snackbar";
import { promiseMutex } from "WoltLabSuite/Core/Helper/PromiseMutex";

async function showDialog(url: string): Promise<void> {
  const { ok } = await dialogFactory().usingFormBuilder().fromEndpoint<Response>(url);

  if (ok) {
    showDefaultSuccessSnackbar().addEventListener("snackbar:close", () => {
      window.location.reload();
    });
  }
}

export function setup(button: HTMLElement): void {
  button.addEventListener(
    "click",
    promiseMutex(() => showDialog(button.dataset.url!)),
  );
}
