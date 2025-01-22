define(["require", "exports", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Helper/Selector"], function (require, exports, Core_1, Selector_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.Selection = void 0;
    class Selection {
        #markAll = null;
        #table;
        constructor(table) {
            this.#table = table;
            this.#markAll = this.#table.querySelector(".gridView__selectAllRows");
            this.#markAll?.addEventListener("change", () => {
                this.#change(this.#markAll.checked);
            });
            (0, Selector_1.wheneverFirstSeen)(`#${this.#table.id} .gridView__selectRow`, (checkbox) => {
                checkbox.addEventListener("change", () => {
                    this.#change();
                });
            });
            this.#restoreSelection();
        }
        refresh() {
            this.#restoreSelection();
        }
        getSelectedIds() {
            const json = window.localStorage.getItem(this.#getStorageKey());
            if (typeof json !== "string") {
                return [];
            }
            let selectedIds = [];
            try {
                const value = JSON.parse(json);
                if (Array.isArray(value)) {
                    selectedIds = value;
                }
            }
            catch {
                if (window.ENABLE_DEBUG_MODE) {
                    console.error("Failed to deserialize the selection.", json);
                }
                return [];
            }
            return selectedIds;
        }
        #change(forceValue, skipStorage = false) {
            const checkboxes = Array.from(this.#table.querySelectorAll(".gridView__selectRow"));
            if (forceValue === undefined) {
                if (this.#markAll !== null) {
                    const markedCheckboxes = checkboxes.filter((checkbox) => checkbox.checked).length;
                    this.#markAll.indeterminate = markedCheckboxes > 0 && markedCheckboxes !== checkboxes.length;
                }
            }
            else {
                for (const checkbox of checkboxes) {
                    checkbox.checked = forceValue;
                }
            }
            if (!skipStorage) {
                this.#saveSelection(checkboxes);
            }
        }
        #saveSelection(checkboxes) {
            const selection = new Map();
            checkboxes.forEach((checkbox) => {
                const row = checkbox.closest(".gridView__row");
                const id = parseInt(row.dataset.objectId);
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
        #restoreSelection() {
            const selectedIds = this.getSelectedIds();
            this.#table.querySelectorAll(".gridView__row").forEach((row) => {
                const id = parseInt(row.dataset.objectId);
                if (!selectedIds.includes(id)) {
                    return;
                }
                const checkbox = row.querySelector(".gridView__selectRow");
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
            this.#change(undefined, true);
        }
        #getStorageKey() {
            return (0, Core_1.getStoragePrefix)() + `gridView-${this.#table.id}-selection`;
        }
    }
    exports.Selection = Selection;
    exports.default = Selection;
});
