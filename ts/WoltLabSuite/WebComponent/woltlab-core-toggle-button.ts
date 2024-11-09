/**
 * `<woltlab-core-toggle-button>` creates a toggle button.
 * Usage: `<woltlab-core-toggle-button name="foo" checked></woltlab-core-toggle-button>`
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */

{
  class WoltlabCoreToggleButtonElement extends HTMLElement {
    static get observedAttributes() {
      return ["checked"];
    }

    constructor() {
      super();

      const shadow = this.attachShadow({ mode: "open" });
      const style = document.createElement("style");
      style.textContent = `
        :host {
          display: inline-flex;
          vertical-align: middle;
        }
      
        [part="track"] {
          position: relative;
          border-radius: 14px;
          width: 40px;
          height: 24px;
          cursor: pointer;
          background-color: var(--wcfSidebarDimmedText);
          transition: 0.4s;
        }

        :host([checked]) [part="track"] {
          background-color: var(--wcfStatusSuccessText);
        }

        [part="slider"] {
          background-color: white;
          border-radius: 50%;
          position: absolute;
          top: 2px;
          bottom: 2px;
          left: 0;
          align-items: center;
          display: flex;
          transition: 0.4s;
          transform: translateX(2px);
        }

        :host([checked]) [part="slider"] {
          transform: translateX(18px);
        }

        ::slotted(fa-icon) {
          color: var(--wcfSidebarDimmedText);
        }

        :host([checked]) ::slotted(fa-icon) {
          color: var(--wcfStatusSuccessText);
        }
      `;

      const track = document.createElement("div");
      track.setAttribute("part", "track");
      const slider = document.createElement("span");
      slider.setAttribute("part", "slider");
      track.append(slider);
      const iconSlot = document.createElement("slot");
      iconSlot.name = "icon";
      slider.append(iconSlot);
      const checkboxSlot = document.createElement("slot");
      checkboxSlot.name = "checkbox";
      shadow.append(style, track, checkboxSlot);

      this.addEventListener("click", (event) => {
        event.stopPropagation();
        event.preventDefault();
        this.toggle();
      });
      this.addEventListener("keydown", (event) => {
        if (event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          this.toggle();
        }
      });
    }

    connectedCallback() {
      this.innerHTML = "";

      this.#renderCheckbox();
      this.#renderIcon();

      this.setAttribute("role", "switch");
      this.setAttribute("tabindex", "0");
    }

    #renderCheckbox(): void {
      if (!this.hasAttribute("name")) {
        return;
      }

      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.name = this.getAttribute("name")!;
      checkbox.value = this.hasAttribute("value") ? this.getAttribute("value")! : "1";
      checkbox.checked = this.checked;
      checkbox.hidden = true;
      checkbox.slot = "checkbox";
      this.append(checkbox);
    }

    #renderIcon(): void {
      const icon = document.createElement("fa-icon");
      icon.setIcon(this.checked ? "check" : "xmark");
      icon.slot = "icon";
      this.append(icon);
    }

    get checked(): boolean {
      return this.hasAttribute("checked");
    }

    set checked(value: boolean) {
      this.toggleAttribute("checked", value);
      this.querySelector("fa-icon")?.setIcon(value ? "check" : "xmark");
    }

    toggle(): void {
      this.checked = !this.checked;
    }

    attributeChangedCallback(name: string) {
      if (name === "checked") {
        this.setAttribute("aria-checked", this.checked.toString());
        this.dispatchEvent(
          new CustomEvent("change", {
            detail: {
              checked: this.checked,
            },
          }),
        );
      }
    }
  }

  window.customElements.define("woltlab-core-toggle-button", WoltlabCoreToggleButtonElement);
}
