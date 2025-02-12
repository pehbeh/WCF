/**
 * Handles interactions that open a form builder dialog.
 *
 * @author Marcel Werk
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */

import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { showDefaultSuccessSnackbar } from "WoltLabSuite/Core/Component/Snackbar";

async function handleFormBuilderDialogAction(element: HTMLElement, endpoint: string): Promise<void> {
  const { ok } = await dialogFactory().usingFormBuilder().fromEndpoint(endpoint);

  if (!ok) {
    return;
  }

  element.dispatchEvent(
    new CustomEvent("interaction:invalidate", {
      bubbles: true,
    }),
  );

  showDefaultSuccessSnackbar();
}

export function setup(identifier: string, container: HTMLElement): void {
  container.addEventListener("interaction:execute", (event: CustomEvent) => {
    if (event.detail.interaction === identifier) {
      void handleFormBuilderDialogAction(event.target as HTMLElement, event.detail.endpoint);
    }
  });
}
