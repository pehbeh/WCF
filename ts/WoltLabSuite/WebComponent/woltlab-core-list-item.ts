// eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
export class WoltlabCoreListItemElement extends HTMLElement {
  #shadow: ShadowRoot | undefined = undefined;

  connectedCallback() {
    this.role = "option";
    this.setAttribute("aria-selected", String(this.selected));
    this.tabIndex = this.selected ? 0 : -1;

    this.addEventListener("click", () => {
      this.selected = true;

      const event = new CustomEvent<WoltlabCoreListItemChangePayload>("change", {
        detail: {
          selected: this.selected,
        },
      });
      this.dispatchEvent(event);
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

:host(:not([selected])) .icon {
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
    `;

    const iconWrapper = document.createElement("div");
    iconWrapper.classList.add("icon");

    const iconSlot = document.createElement("slot");
    iconSlot.name = "icon";
    iconWrapper.append(iconSlot);

    const contentWrapper = document.createElement("div");
    contentWrapper.classList.add("content");

    const elements = document.createElement("slot");
    contentWrapper.append(elements);

    shadow.append(style, iconWrapper, contentWrapper);

    const icon = document.createElement("fa-icon");
    icon.setIcon("check");
    icon.slot = "icon";

    this.querySelector('slot[name="icon"]')?.remove();

    this.append(icon);

    this.addEventListener("focus", () => {
      this.tabIndex = 0;
    });
    this.addEventListener("blur", () => {
      if (!this.selected) {
        this.tabIndex = -1;
      }
    });
  }

  #getShadow(): ShadowRoot {
    if (this.#shadow === undefined) {
      this.#shadow = this.attachShadow({ mode: "open" });
    }

    return this.#shadow;
  }

  get selected(): boolean {
    return this.hasAttribute("selected");
  }

  set selected(selected: boolean) {
    if (selected) {
      this.setAttribute("aria-selected", "true");
      this.setAttribute("selected", "");
      this.tabIndex = 0;
    } else {
      this.setAttribute("aria-selected", "false");
      this.removeAttribute("selected");
      this.tabIndex = -1;
    }
  }

  get value(): string {
    return this.getAttribute("value") || "";
  }
}

window.customElements.define("woltlab-core-list-item", WoltlabCoreListItemElement);

type WoltlabCoreListItemChangePayload = { selected: boolean };

interface WoltlabCoreListItemEventMap {
  change: CustomEvent<WoltlabCoreListItemChangePayload>;
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
