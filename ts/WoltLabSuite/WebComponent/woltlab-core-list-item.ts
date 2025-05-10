let idCounter = 0;

// eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
export class WoltlabCoreListItemElement extends HTMLElement {
  #shadow: ShadowRoot | undefined = undefined;
  readonly #checkbox: HTMLInputElement;

  constructor() {
    super();

    this.#checkbox = document.createElement("input");
    this.#checkbox.type = "checkbox";
    this.#checkbox.inert = true;
  }

  connectedCallback() {
    this.role = "option";
    this.classList.add("listBox__item");

    if (this.#isMultiple()) {
      this.ariaChecked = String(this.selected);
      this.removeAttribute("aria-selected");
    } else {
      this.ariaSelected = String(this.selected);
      this.removeAttribute("aria-checked");
    }

    if (!this.id) {
      this.id = "woltlabCoreListItem" + idCounter++;
    }

    this.addEventListener("click", () => {
      this.toggle();
    });

    const shadow = this.#getShadow();
    shadow.innerHTML = "";

    const style = document.createElement("style");
    style.textContent = `
:host {
  align-items: center;
  column-gap: 10px;
  color: var(--wcfDropdownLinkActive);
  display: grid;
  grid-template-columns: 16px auto;
  grid-template-areas: "icon content";
  max-width: 350px;
  padding: 6px 12px;
}

@media (hover: hover) {
  :host(:hover) {
    background-color: var(--wcfDropdownBackgroundActive);
    cursor: pointer;
  }
}

html[data-color-scheme="dark"] :host {
  border: 1px solid var(--wcfDropdownBorderInner);
}

.icon {
  grid-area: icon;
}

:host(:not([aria-selected="true"])) .icon ::slotted(fa-icon) {
  visibility: hidden;
}

.content {
  cursor: pointer;
  grid-area: content;
  overflow: hidden;
  text-decoration: none;
  text-overflow: ellipsis;
  user-select: none;
  white-space: nowrap;
  word-wrap: normal;
}

input {
  pointer-events: none;
}
    `;

    const iconWrapper = document.createElement("div");
    iconWrapper.classList.add("icon");
    iconWrapper.ariaHidden = "true";

    const iconSlot = document.createElement("slot");
    iconSlot.name = "icon";
    iconWrapper.append(iconSlot);

    const contentWrapper = document.createElement("div");
    contentWrapper.classList.add("content");

    const elements = document.createElement("slot");
    contentWrapper.append(elements);

    shadow.append(style, iconWrapper, contentWrapper);

    this.querySelector('fa-icon[slot="icon"]')?.remove();

    if (this.#isMultiple()) {
      iconWrapper.append(this.#checkbox);
    } else {
      const icon = document.createElement("fa-icon");
      icon.setIcon("check");
      icon.slot = "icon";

      this.append(icon);
    }
  }

  #getShadow(): ShadowRoot {
    if (this.#shadow === undefined) {
      this.#shadow = this.attachShadow({ mode: "open" });
    }

    return this.#shadow;
  }

  #isMultiple(): boolean {
    // We cannot use a proper `instanceof` check here as it would create a
    // circular dependency.
    if (this.parentElement?.tagName === "WOLTLAB-CORE-LIST-BOX") {
      return this.parentElement.hasAttribute("multiple");
    }

    return false;
  }

  toggle(): void {
    let hasChanged = false;
    if (this.#isMultiple()) {
      this.selected = !this.selected;

      hasChanged = true;
    } else if (!this.selected) {
      this.selected = true;

      hasChanged = true;
    }

    if (hasChanged) {
      const event = new CustomEvent<void>("change");
      this.dispatchEvent(event);
    }
  }

  get selected(): boolean {
    return this.ariaSelected === "true" || this.ariaChecked === "true";
  }

  set selected(selected: boolean) {
    if (selected === this.selected) {
      return;
    }

    if (this.#isMultiple()) {
      this.#checkbox.checked = selected;

      if (selected) {
        this.ariaChecked = "true";
      } else {
        this.ariaChecked = "false";
      }
    } else {
      if (selected) {
        this.ariaSelected = "true";
      } else {
        this.ariaSelected = "false";
      }
    }
  }

  get focused(): boolean {
    return this.hasAttribute("focused");
  }

  set focused(focused: boolean) {
    if (focused) {
      this.setAttribute("focused", "");
    } else {
      this.removeAttribute("focused");
    }
  }

  get value(): string {
    return this.getAttribute("value") || "";
  }

  set value(value: string) {
    this.setAttribute("value", value);
  }
}

window.customElements.define("woltlab-core-list-item", WoltlabCoreListItemElement);

interface WoltlabCoreListItemEventMap {
  change: CustomEvent<void>;
}

// eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
export interface WoltlabCoreListItemElement extends HTMLElement {
  addEventListener: {
    <T extends keyof WoltlabCoreListItemEventMap>(
      type: T,
      listener: (this: WoltlabCoreListItemElement, ev: WoltlabCoreListItemEventMap[T]) => any,
      options?: boolean | AddEventListenerOptions,
    ): void;
  } & HTMLElement["addEventListener"];
}
