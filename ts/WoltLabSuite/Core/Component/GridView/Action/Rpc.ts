import { deleteObject } from "WoltLabSuite/Core/Api/DeleteObject";
import { postObject } from "WoltLabSuite/Core/Api/PostObject";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { ConfirmationType, handleConfirmation } from "./Confirmation";

async function handleRpcAction(
  row: HTMLTableRowElement,
  objectName: string,
  endpoint: string,
  confirmationType: ConfirmationType,
  customConfirmationMessage: string = "",
): Promise<void> {
  const confirmationResult = await handleConfirmation(objectName, confirmationType, customConfirmationMessage);
  if (!confirmationResult.result) {
    return;
  }

  if (confirmationType == ConfirmationType.Delete) {
    const result = await deleteObject(endpoint);
    if (!result.ok) {
      return;
    }
  } else {
    const result = await postObject(
      endpoint,
      confirmationResult.reason ? { reason: confirmationResult.reason } : undefined,
    );
    if (!result.ok) {
      return;
    }
  }

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
    if (event.detail.action === "rpc") {
      void handleRpcAction(
        event.target as HTMLTableRowElement,
        event.detail.objectName,
        event.detail.endpoint,
        event.detail.confirmationType,
        event.detail.confirmationMessage,
      );
    }
  });
}
