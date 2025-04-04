import HTMLParsedElement from "./html-parsed-element";
import { WoltlabCoreListItemElement } from "./woltlab-core-list-item";

{
  type ChangePayload = { selected: string };

  interface WoltlabCoreListBoxEventMap {
    change: CustomEvent<ChangePayload>;
  }

  // eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
  class WoltlabCoreListBoxElement extends HTMLParsedElement {
    #position = -1;
    #selected = "";
    readonly #formInput: HTMLInputElement;
    readonly #knownItems: WeakSet<WoltlabCoreListItemElement> = new WeakSet();
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

.content {
  overflow: auto;
}
      `;

      const container = document.createElement("div");
      container.classList.add("content");

      this.#slot = document.createElement("slot");
      container.append(this.#slot);

      this.#shadow = this.attachShadow({ mode: "open" });
      this.#shadow.append(style, container);

      this.#formInput = document.createElement("input");
      this.#formInput.type = "hidden";

      this.addEventListener("focus", () => {
        const items = this.#getItems();
        if (items.length === 0) {
          return;
        }

        let position = items.findIndex((item) => item.selected);
        if (position === -1) {
          position = 0;
        }

        this.#setFocus(items, position);
      });

      this.addEventListener("keydown", (event) => {
        if (event.key.length === 1) {
          event.preventDefault();
          this.#focusFirstMatchingItem(event.key);
          return;
        }

        switch (event.key) {
          case "ArrowDown":
            event.preventDefault();
            this.#focusNextItem();
            break;

          case "ArrowUp":
            event.preventDefault();
            this.#focusPreviousItem();
            break;

          case "End":
            event.preventDefault();
            this.#focusLastItem();
            break;

          case "Enter":
            event.preventDefault();
            this.#selectItem();
            break;

          case "Home":
            event.preventDefault();
            this.#focusFirstItem();
            break;
        }
      });
    }

    parsedCallback() {
      this.classList.add("listBox");
      this.role = "listbox";
      this.ariaMultiSelectable = "false";
      this.ariaOrientation = "vertical";
      this.tabIndex = 0;

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

        if (!this.#knownItems.has(element)) {
          this.#knownItems.add(element);

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

      for (const item of this.#getItems()) {
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

    #focusNextItem(): void {
      const items = this.#getItems();
      const size = items.length;
      if (size === 0) {
        return;
      }

      let position = this.#position + 1;
      if (position >= size) {
        position = size - 1;
      }

      if (position === this.#position) {
        return;
      }

      this.#setFocus(items, position);
    }

    #focusPreviousItem(): void {
      const items = this.#getItems();
      if (items.length === 0) {
        return;
      }

      let position = this.#position - 1;
      if (position < 0) {
        position = 0;
      }

      if (position === this.#position) {
        return;
      }

      this.#setFocus(items, position);
    }

    #focusFirstItem(): void {
      const items = this.#getItems();
      if (items.length === 0) {
        return;
      }

      this.#setFocus(items, 0);
    }

    #focusLastItem(): void {
      const items = this.#getItems();
      if (items.length === 0) {
        return;
      }

      this.#setFocus(items, items.length - 1);
    }

    #focusFirstMatchingItem(character: string): void {
      const items = this.#getItems();
      const size = items.length;
      if (size === 0) {
        return;
      }

      character = character.toLowerCase();
      for (let position = 0; position < size; position++) {
        if (items[position].textContent!.trim().toLowerCase().startsWith(character)) {
          this.#setFocus(items, position);
          return;
        }
      }
    }

    #setFocus(items: WoltlabCoreListItemElement[], position: number): void {
      for (let i = 0, length = items.length; i < length; i++) {
        const item = items[i];

        if (i === position) {
          this.setAttribute("aria-activedescendant", item.id);
          item.focused = true;

          this.#scrollItemIntoView(item);
        } else {
          item.focused = false;
        }
      }

      this.#position = position;
    }

    #scrollItemIntoView(item: WoltlabCoreListItemElement): void {
      if ("scrollIntoViewIfNeeded" in item) {
        (item as any).scrollIntoViewIfNeeded(false);

        return;
      }

      // See https://github.com/nuxodin/lazyfill/blob/c53e43fe2d88269cf84b924461218c23422cc49a/polyfills/Element/prototype/scrollIntoViewIfNeeded.js
      const observer = new IntersectionObserver(([entry]) => {
        const ratio = entry.intersectionRatio;
        if (ratio < 1) {
          item.scrollIntoView({
            block: "nearest",
            inline: "nearest",
          });
        }
        observer.disconnect();
      });
      observer.observe(item);
    }

    #selectItem(): void {
      const item = this.#getItems()[this.#position];
      if (item === undefined) {
        return;
      }

      this.#changeSelection(item.value);
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

    #getItems(): WoltlabCoreListItemElement[] {
      return Array.from(this.#slot.assignedElements()).filter(
        (element) => element instanceof WoltlabCoreListItemElement,
      );
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
