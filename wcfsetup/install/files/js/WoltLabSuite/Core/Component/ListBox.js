define(["require", "exports", "tslib", "focus-trap", "../Ui/Alignment", "../Helper/PageOverlay", "../Ui/CloseOverlay"], function (require, exports, tslib_1, focus_trap_1, Alignment_1, PageOverlay_1, CloseOverlay_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.ListBox = void 0;
    CloseOverlay_1 = tslib_1.__importDefault(CloseOverlay_1);
    class ListBox extends EventTarget {
        #anchor;
        #container;
        #element;
        #focusTrap = undefined;
        constructor(items, selected, anchor) {
            super();
            this.#anchor = anchor;
            this.#element = this.#createElement(items, selected);
            this.#container = this.#createContainer();
        }
        #createElement(items, selected) {
            const listBox = document.createElement("woltlab-core-list-box");
            listBox.tabIndex = 0;
            for (const [value, html] of items) {
                const listItem = document.createElement("woltlab-core-list-item");
                listItem.value = value;
                listItem.innerHTML = html;
                listBox.append(listItem);
            }
            if (typeof selected === "string") {
                listBox.selected = selected;
            }
            else {
                listBox.multiple = true;
                listBox.selectedValues = selected;
            }
            listBox.addEventListener("change", () => {
                if (listBox.multiple) {
                    const event = new CustomEvent("selectedValues", {
                        detail: {
                            selectedValues: listBox.selectedValues,
                        },
                    });
                    this.dispatchEvent(event);
                }
                else {
                    const event = new CustomEvent("selected", {
                        detail: {
                            selected: listBox.selected,
                        },
                    });
                    this.dispatchEvent(event);
                    this.close();
                }
            });
            return listBox;
        }
        #createContainer() {
            const container = document.createElement("div");
            container.classList.add("listBox__dropdown");
            container.addEventListener("click", (event) => {
                event.stopPropagation();
            });
            container.append(this.#element);
            return container;
        }
        open() {
            if (this.#focusTrap !== undefined) {
                throw new Error("The list box is already open.");
            }
            CloseOverlay_1.default.execute();
            (0, PageOverlay_1.getPageOverlayContainer)().append(this.#container);
            this.#focusTrap = (0, focus_trap_1.createFocusTrap)(this.#container, {
                allowOutsideClick: true,
                escapeDeactivates: () => {
                    this.close();
                    return false;
                },
            });
            CloseOverlay_1.default.add("WoltLabSuite/Core/Component/ListBox", () => {
                this.close();
            });
            this.#focusTrap.activate();
            (0, Alignment_1.set)(this.#container, this.#anchor);
            this.#element.addEventListener("change", () => { });
        }
        close() {
            if (this.#focusTrap === undefined) {
                throw new Error("The list box is not open.");
            }
            this.#focusTrap.deactivate();
            this.#container.remove();
            CloseOverlay_1.default.remove("WoltLabSuite/Core/Component/ListBox");
            this.#focusTrap = undefined;
        }
    }
    exports.ListBox = ListBox;
    exports.default = ListBox;
});
