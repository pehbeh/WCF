import { wheneverFirstSeen } from "WoltLabSuite/Core/Helper/Selector";
import { postObject } from "WoltLabSuite/Core/Api/PostObject";

async function handleToggle(checked: boolean, enableEndpoint: string, disableEndpoint: string): Promise<void> {
  await postObject(checked ? enableEndpoint : disableEndpoint);
}

export function setup(tableId: string): void {
  wheneverFirstSeen(`#${tableId} .gridView__row woltlab-core-toggle-button`, (toggleButton) => {
    toggleButton.addEventListener("change", (event: CustomEvent) => {
      void handleToggle(
        event.detail.checked as boolean,
        toggleButton.dataset.enableEndpoint!,
        toggleButton.dataset.disableEndpoint!,
      );
    });
  });
}
