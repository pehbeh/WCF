import { createFocusTrap, FocusTrap } from "focus-trap";
import { set as setPosition } from "../Ui/Alignment";
import { getPageOverlayContainer } from "../Helper/PageOverlay";
import UiCloseOverlay from "../Ui/CloseOverlay";

type Value = string;
type Html = string;
type Item = [Value, Html];

export class ListBox extends EventTarget {
  readonly #anchor: HTMLElement;
  readonly #container: HTMLElement;
  readonly #element: WoltlabCoreListBoxElement;
  #focusTrap: FocusTrap | undefined = undefined;

  constructor(items: Item[], selected: string | string[], anchor: HTMLElement) {
    super();

    this.#anchor = anchor;
    this.#element = this.#createElement(items, selected);
    this.#container = this.#createContainer();
  }

  #createElement(items: Item[], selected: string | string[]): WoltlabCoreListBoxElement {
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
    } else {
      listBox.multiple = true;
      listBox.selectedValues = selected;
    }

    listBox.addEventListener("change", () => {
      if (listBox.multiple) {
        const event = new CustomEvent("selectedValues", {
          detail: {
            selectedValues: listBox.selectedValues!,
          },
        });
        this.dispatchEvent(event);
      } else {
        const event = new CustomEvent("selected", {
          detail: {
            selected: listBox.selected!,
          },
        });
        this.dispatchEvent(event);

        this.close();
      }
    });

    return listBox;
  }

  #createContainer(): HTMLElement {
    const container = document.createElement("div");
    container.classList.add("listBox__dropdown");
    container.addEventListener("click", (event) => {
      event.stopPropagation();
    });
    container.append(this.#element);

    return container;
  }

  open(): void {
    if (this.#focusTrap !== undefined) {
      throw new Error("The list box is already open.");
    }

    UiCloseOverlay.execute();

    getPageOverlayContainer().append(this.#container);
    this.#focusTrap = createFocusTrap(this.#container, {
      allowOutsideClick: true,
      escapeDeactivates: () => {
        this.close();

        return false;
      },
    });
    UiCloseOverlay.add("WoltLabSuite/Core/Component/ListBox", () => {
      this.close();
    });

    this.#focusTrap.activate();
    setPosition(this.#container, this.#anchor);

    this.#element.addEventListener("change", () => {});
  }

  close(): void {
    if (this.#focusTrap === undefined) {
      throw new Error("The list box is not open.");
    }

    this.#focusTrap.deactivate();
    this.#container.remove();
    UiCloseOverlay.remove("WoltLabSuite/Core/Component/ListBox");

    this.#focusTrap = undefined;
  }
}

export default ListBox;
