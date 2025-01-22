import { getStoragePrefix } from "WoltLabSuite/Core/Core";
import { wheneverFirstSeen } from "WoltLabSuite/Core/Helper/Selector";

export class Selection {
  readonly #markAll: HTMLInputElement | null = null;
  readonly #table: HTMLTableElement;

  constructor(table: HTMLTableElement) {
    this.#table = table;

    this.#markAll = this.#table.querySelector<HTMLInputElement>(".gridView__selectAllRows");
    this.#markAll?.addEventListener("change", () => {
      this.#change(this.#markAll!.checked);
    });

    wheneverFirstSeen(`#${this.#table.id} .gridView__selectRow`, (checkbox: HTMLInputElement) => {
      checkbox.addEventListener("change", () => {
        this.#change();
      });
    });

    this.#restoreSelection();
  }

  refresh(): void {
    this.#restoreSelection();
  }

  getSelectedIds(): number[] {
    const json = window.localStorage.getItem(this.#getStorageKey());
    if (typeof json !== "string") {
      return [];
    }

    let selectedIds: number[] = [];
    try {
      const value = JSON.parse(json);
      if (Array.isArray(value)) {
        selectedIds = value;
      }
    } catch {
      if (window.ENABLE_DEBUG_MODE) {
        console.error("Failed to deserialize the selection.", json);
      }

      return [];
    }

    return selectedIds;
  }

  #change(forceValue?: boolean, skipStorage = false): void {
    const checkboxes = Array.from(this.#table.querySelectorAll<HTMLInputElement>(".gridView__selectRow"));
    if (forceValue === undefined) {
      if (this.#markAll !== null) {
        const markedCheckboxes = checkboxes.filter((checkbox) => checkbox.checked).length;
        this.#markAll.indeterminate = markedCheckboxes > 0 && markedCheckboxes !== checkboxes.length;
      }
    } else {
      for (const checkbox of checkboxes) {
        checkbox.checked = forceValue;
      }
    }

    if (!skipStorage) {
      this.#saveSelection(checkboxes);
    }
  }

  #saveSelection(checkboxes: HTMLInputElement[]): void {
    const selection = new Map<number, boolean>();
    checkboxes.forEach((checkbox) => {
      const row = checkbox.closest(".gridView__row") as HTMLElement;
      const id = parseInt(row.dataset.objectId!);

      selection.set(id, checkbox.checked);
    });

    // We support selection across pages thus we need to preserve the selection
    // of objects that are not present on the current page.
    const selectedIds = this.getSelectedIds().filter((id) => {
      const checked = selection.get(id);
      if (checked === undefined) {
        // Object does not appear on this page, preserve the id.
        return true;
      }

      return checked;
    });

    // Add any id that was previously not part of the stored selection.
    selection.forEach((checked, id) => {
      if (checked && !selectedIds.includes(id)) {
        selectedIds.push(id);
      }
    });

    window.localStorage.setItem(this.#getStorageKey(), JSON.stringify(selectedIds));
  }

  #restoreSelection(): void {
    const selectedIds = this.getSelectedIds();

    this.#table.querySelectorAll(".gridView__row").forEach((row: HTMLElement) => {
      const id = parseInt(row.dataset.objectId!);
      if (!selectedIds.includes(id)) {
        return;
      }

      const checkbox = row.querySelector<HTMLInputElement>(".gridView__selectRow");
      if (checkbox) {
        checkbox.checked = true;
      }
    });

    this.#change(undefined, true);
  }

  #getStorageKey(): string {
    return getStoragePrefix() + `gridView-${this.#table.id}-selection`;
  }
}

export default Selection;
