import HTMLParsedElement from "./html-parsed-element";
import { WoltlabCoreListItemElement } from "./woltlab-core-list-item";

{
  type ChangePayload = { selected: string };

  interface WoltlabCoreListBoxEventMap {
    change: CustomEvent<ChangePayload>;
  }

  // eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
  class WoltlabCoreListBoxElement extends HTMLParsedElement {
    #input: HTMLInputElement | undefined = undefined;
    #selected = "";
    #shadow: ShadowRoot | undefined = undefined;
    readonly #items: Set<WoltlabCoreListItemElement> = new Set();

    connectedCallback() {
      this.role = "listbox";
      this.setAttribute("aria-multiselectable", "false");
      this.setAttribute("aria-orientation", "vertical");

      const shadow = this.#getShadow();
      shadow.innerHTML = "";

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

      const elements = document.createElement("slot");
      elements.addEventListener("slotchange", () => {
        for (const element of elements.assignedElements()) {
          if (element instanceof WoltlabCoreListItemElement) {
            if (element.selected) {
              if (this.#selected === "") {
                this.#selected = element.value;
                this.setAttribute("selected", this.#selected);

                if (this.#input !== undefined) {
                  this.#input.value = element.value;
                }
              } else {
                element.selected = false;
              }
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
        }
      });

      shadow.append(style, elements);

      const name = this.getAttribute("name") || "";
      if (name !== "") {
        this.#input = document.createElement("input");
        this.#input.type = "hidden";
        this.#input.name = name;
        this.#input.value = this.#selected;

        this.removeAttribute("name");
      }

      if (this.#input !== undefined) {
        this.append(this.#input);
      }
    }

    parsedCallback(): void {
      console.log("parsedCallback()");
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

      if (this.#input !== undefined) {
        this.#input.value = value;
      }

      const event = new CustomEvent<ChangePayload>("change", {
        detail: {
          selected: value,
        },
      });
      this.dispatchEvent(event);
    }

    #getShadow(): ShadowRoot {
      if (this.#shadow === undefined) {
        this.#shadow = this.attachShadow({ mode: "open" });
      }

      return this.#shadow;
    }

    get selected(): string {
      return this.#selected;
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
