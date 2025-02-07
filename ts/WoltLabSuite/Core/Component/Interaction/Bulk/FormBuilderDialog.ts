/**
 * Handles bulk interactions that open a form builder dialog.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */

import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { showDefaultSuccessSnackbar } from "WoltLabSuite/Core/Component/Snackbar";

async function handleFormBuilderDialogAction(
  container: HTMLElement,
  objectIds: number[],
  endpoint: string,
): Promise<void> {
  const { ok } = await dialogFactory().usingFormBuilder().fromEndpoint(endpoint);

  if (!ok) {
    return;
  }

  for (let i = 0; i < objectIds.length; i++) {
    const element = container.querySelector(`[data-object-id="${objectIds[i]}"]`);
    if (!element) {
      continue;
    }

    element.dispatchEvent(
      new CustomEvent("interaction:invalidate", {
        bubbles: true,
      }),
    );
  }

  showDefaultSuccessSnackbar();

  container.dispatchEvent(new CustomEvent("interaction:reset-selection"));
}

export function setup(identifier: string, container: HTMLElement): void {
  container.addEventListener("bulk-interaction", (event: CustomEvent) => {
    if (event.detail.bulkInteraction === identifier) {
      void handleFormBuilderDialogAction(container, JSON.parse(event.detail.objectIds), event.detail.endpoint);
    }
  });
}
