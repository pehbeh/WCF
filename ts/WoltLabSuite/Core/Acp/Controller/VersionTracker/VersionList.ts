/**
 * Handles the list of versions in the version tracker list.
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */

import { revertVersion } from "WoltLabSuite/Core/Api/VersionTrackers/RevertVersion";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

function initRevertButtons(container: HTMLElement, objectType: string, objectId: number): void {
  container.querySelectorAll<HTMLButtonElement>(".jsRevertButton").forEach((button) => {
    button.addEventListener("click", async () => {
      const result = await confirmationFactory().custom(button.dataset.confirmMessage!).withoutMessage();
      if (!result) {
        return;
      }

      const response = await revertVersion(objectType, objectId, parseInt(button.dataset.objectId!));
      if (response.ok) {
        UiNotification.show(undefined, () => {
          window.location.reload();
        });
      }
    });
  });
}

function initRadioButtons(container: HTMLElement): void {
  const oldIdInputs = container.querySelectorAll<HTMLInputElement>("input[name=oldID]");
  const newIdInputs = container.querySelectorAll<HTMLInputElement>("input[name=newID]");

  function newInputChanged(newIdInput: HTMLInputElement): void {
    const newId = newIdInput.value === "current" ? Infinity : parseInt(newIdInput.value);

    oldIdInputs.forEach((oldIdInput) => {
      const oldId = oldIdInput.value === "current" ? Infinity : parseInt(oldIdInput.value);

      oldIdInput.disabled = oldId >= newId;
    });
  }

  newIdInputs.forEach((newIdInput) => {
    newIdInput.addEventListener("change", () => {
      newInputChanged(newIdInput);
    });
    if (newIdInput.checked) {
      newInputChanged(newIdInput);
    }
  });

  function oldInputChanged(oldIdInput: HTMLInputElement): void {
    const oldId = oldIdInput.value === "current" ? Infinity : parseInt(oldIdInput.value);

    newIdInputs.forEach((newIdInput) => {
      const newId = newIdInput.value === "current" ? Infinity : parseInt(newIdInput.value);

      newIdInput.disabled = newId <= oldId;
    });
  }

  oldIdInputs.forEach((oldIdInput) => {
    oldIdInput.addEventListener("change", () => {
      oldInputChanged(oldIdInput);
    });
    if (oldIdInput.checked) {
      oldInputChanged(oldIdInput);
    }
  });
}

export function setup(container: HTMLElement, objectType: string, objectId: number): void {
  initRevertButtons(container, objectType, objectId);
  initRadioButtons(container);
}
