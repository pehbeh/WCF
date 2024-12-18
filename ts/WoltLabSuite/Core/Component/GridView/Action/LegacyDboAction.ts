/**
 * Handles execution of DBO actions within grid views.
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @deprecated 6.2 DBO actions are considered outdated and should be migrated to RPC endpoints.
 */

import { dboAction } from "WoltLabSuite/Core/Ajax";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { ConfirmationType, handleConfirmation } from "./Confirmation";

async function handleDboAction(
  row: HTMLTableRowElement,
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
    .objectIds([parseInt(row.dataset.objectId!)])
    .payload(confirmationResult.reason ? { reason: confirmationResult.reason } : {})
    .dispatch();

  if (confirmationType == ConfirmationType.Delete) {
    row.remove();
  } else {
    row.dispatchEvent(
      new CustomEvent("refresh", {
        bubbles: true,
      }),
    );

    // TODO: This shows a generic success message and should be replaced with a more specific message.
    showNotification();
  }
}

export function setup(table: HTMLTableElement): void {
  table.addEventListener("action", (event: CustomEvent) => {
    if (event.detail.action === "legacy-dbo-action") {
      void handleDboAction(
        event.target as HTMLTableRowElement,
        event.detail.objectName,
        event.detail.className,
        event.detail.actionName,
        event.detail.confirmationType,
        event.detail.confirmationMessage,
      );
    }
  });
}
