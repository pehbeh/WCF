import HTMLParsedElement from "./html-parsed-element";
import { WoltlabCoreListItemElement } from "./woltlab-core-list-item";

{
  type ChangePayload = { selected: string };

  interface WoltlabCoreListBoxEventMap {
    change: CustomEvent<ChangePayload>;
  }

  // eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
  class WoltlabCoreListBoxElement extends HTMLParsedElement {
    #selected = "";
    readonly #formInput: HTMLInputElement;
    readonly #items: Set<WoltlabCoreListItemElement> = new Set();
    readonly #shadow: ShadowRoot;
    readonly #slot: HTMLSlotElement;

    constructor() {
      super();

      const style = document.createElement("style");
      style.textContent = `
:host {
  background-color: var(--wcfDropdownBackground);
	border-radius: 4px;
	box-shadow: var(--wcfBoxShadow);
	color: var(--wcfDropdownText);
  display: flex;
  flex-direction: column;
	min-width: 160px !important;
	padding: 4px 0;
	pointer-events: all;
	position: fixed;
	text-align: left;
	z-index: 450;
}
    `;

      this.#slot = document.createElement("slot");

      this.#shadow = this.attachShadow({ mode: "open" });
      this.#shadow.append(style, this.#slot);

      this.#formInput = document.createElement("input");
      this.#formInput.type = "hidden";
    }

    parsedCallback() {
      this.role = "listbox";
      this.setAttribute("aria-multiselectable", "false");
      this.setAttribute("aria-orientation", "vertical");

      const selected = this.getAttribute("selected") || this.#selected;
      this.removeAttribute("selected");

      let foundValue = false;
      for (const element of this.#slot.assignedElements()) {
        if (!(element instanceof WoltlabCoreListItemElement)) {
          continue;
        }

        if (element.value === selected) {
          element.selected = true;

          if (this.#formInput !== undefined) {
            this.#formInput.value = this.#selected;
          }

          foundValue = true;
        } else {
          element.selected = false;
        }

        if (!this.#items.has(element)) {
          this.#items.add(element);

          element.addEventListener("change", (event) => {
            if (event.detail.selected) {
              this.#changeSelection(element.value);
            } else {
              throw new Error("TODO: not implemented");
            }
          });
        }
      }

      this.#selected = foundValue ? selected : "";
      this.#updateFormInput(this.name);
    }

    #changeSelection(value: string): void {
      this.#selected = value;
      this.setAttribute("selected", value);

      for (const item of this.#items) {
        if (item.selected) {
          if (item.value !== value) {
            item.selected = false;
          }
        } else if (item.value === value) {
          item.selected = true;
        }
      }

      if (this.#formInput !== undefined) {
        this.#formInput.value = value;
      }

      const event = new CustomEvent<ChangePayload>("change", {
        detail: {
          selected: value,
        },
      });
      this.dispatchEvent(event);
    }

    #updateFormInput(name: string): void {
      if (name === "") {
        this.removeAttribute("name");
        this.#formInput.remove();
      } else {
        this.#formInput.name = name;
        this.#formInput.value = this.#selected;

        this.#shadow.append(this.#formInput);
      }
    }

    get selected(): string {
      return this.#selected;
    }

    get name(): string {
      return this.getAttribute("name") || "";
    }

    set name(name: string) {
      this.#updateFormInput(name);
    }
  }

  // eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
  interface WoltlabCoreListBoxElement extends HTMLElement {
    addEventListener: {
      <T extends keyof WoltlabCoreListBoxEventMap>(
        type: T,
        listener: (this: WoltlabCoreListBoxElement, ev: WoltlabCoreListBoxEventMap[T]) => any,
        options?: boolean | AddEventListenerOptions,
      ): void;
    } & HTMLElement["addEventListener"];
  }

  window.customElements.define("woltlab-core-list-box", WoltlabCoreListBoxElement);
}
