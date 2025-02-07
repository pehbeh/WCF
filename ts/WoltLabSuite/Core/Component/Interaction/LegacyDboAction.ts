/**
 * Handles interactions that call legacy DBO actions.
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @deprecated 6.2 DBO actions are considered outdated and should be migrated to RPC endpoints.
 */

import { dboAction } from "WoltLabSuite/Core/Ajax";
import { ConfirmationType, handleConfirmation } from "./Confirmation";
import { showDefaultSuccessSnackbar, showSuccessSnackbar } from "WoltLabSuite/Core/Component/Snackbar";
import { getPhrase } from "WoltLabSuite/Core/Language";

async function handleDboAction(
  element: HTMLElement,
  objectName: string,
  className: string,
  actionName: string,
  confirmationType: ConfirmationType,
  customConfirmationMessage: string = "",
): Promise<void> {
  const confirmationResult = await handleConfirmation(objectName, confirmationType, customConfirmationMessage);
  if (!confirmationResult.result) {
    return;
  }

  await dboAction(actionName, className)
    .objectIds([parseInt(element.dataset.objectId!)])
    .payload(confirmationResult.reason ? { reason: confirmationResult.reason } : {})
    .dispatch();

  if (confirmationType == ConfirmationType.Delete) {
    element.dispatchEvent(
      new CustomEvent("interaction:remove", {
        bubbles: true,
      }),
    );

    showSuccessSnackbar(getPhrase("wcf.global.success.delete"));
  } else {
    element.dispatchEvent(
      new CustomEvent("interaction:invalidate", {
        bubbles: true,
      }),
    );

    showDefaultSuccessSnackbar();
  }
}

export function setup(identifier: string, container: HTMLElement): void {
  container.addEventListener("interaction:execute", (event: CustomEvent) => {
    if (event.detail.interaction === identifier) {
      void handleDboAction(
        event.target as HTMLElement,
        event.detail.objectName,
        event.detail.className,
        event.detail.actionName,
        event.detail.confirmationType,
        event.detail.confirmationMessage,
      );
    }
  });
}
