{
  class WoltlabCoreLabelPickerElement extends HTMLElement {
    #button: HTMLButtonElement | undefined = undefined;
    #input: HTMLInputElement | undefined = undefined;
    #labels: Map<number, string> | undefined = undefined;

    connectedCallback() {
      this.#setupLabels();

      this.innerHTML = "";

      if (!this.value) {
        this.value = 0;
      }

      if (!this.name) {
        this.name = "labelIDs";
      }

      this.classList.add("dropdown");

      if (this.#button === undefined) {
        this.#button = document.createElement("button");
        this.#button.type = "button";
        this.#button.classList.add("dropdownToggle");
        this.#button.append(this.#getLabel(this.value));
      }

      if (this.#input === undefined) {
        this.#input = document.createElement("input");
        this.#input.type = "hidden";
        this.#input.name = this.name;
        this.#input.value = this.value.toString();
      }

      this.append(this.#button, this.#buildSelection(), this.#input);
    }

    #setupLabels(): void {
      if (this.#labels !== undefined) {
        return;
      }

      const labels = JSON.parse(this.getAttribute("labels")!);
      this.#labels = new Map(labels);

      this.removeAttribute("labels");
    }

    #getEmptyLabel(): HTMLElement {
      const label = document.createElement("span");
      label.classList.add("badge", "label");
      label.textContent = window.WoltLabLanguage.getPhrase("wcf.label.none");

      return label;
    }

    #getLabel(labelId: number): Node {
      const html = this.#labels?.get(labelId);
      if (html === undefined) {
        return this.#getEmptyLabel();
      }

      const template = document.createElement("template");
      template.innerHTML = html;

      return template.content.cloneNode(true);
    }

    #buildSelection(): HTMLElement {
      const dropdownMenu = document.createElement("ol");
      dropdownMenu.classList.add("dropdownMenu");

      for (const [labelId, html] of this.#labels!) {
        dropdownMenu.append(this.#createListItem(labelId, html));
      }

      if (!this.required) {
        const divider = document.createElement("li");
        divider.classList.add("dropdownDivider");

        const emptySelection = this.#createListItem(0, this.#getEmptyLabel().outerHTML);
        dropdownMenu.append(divider, emptySelection);
      }

      return dropdownMenu;
    }

    #createListItem(labelId: number, html: string): HTMLLIElement {
      const button = document.createElement("button");
      button.type = "button";
      button.innerHTML = html;
      button.addEventListener("click", () => {
        this.value = labelId;
      });

      const listItem = document.createElement("li");
      listItem.append(button);

      return listItem;
    }

    get value(): number {
      return parseInt(this.getAttribute("value") || "");
    }

    set value(value: number) {
      if (value !== 0 && !this.#labels?.has(value)) {
        throw new Error(`There is no label with the id ${value}.`);
      }

      this.setAttribute("value", value.toString());

      if (this.#button !== undefined && this.#input !== undefined) {
        this.#button.innerHTML = "";
        this.#button.append(this.#getLabel(this.value));

        this.#input.value = value.toString();
      }
    }

    get name(): string {
      return this.getAttribute("name") || "";
    }

    set name(name: string) {
      if (name === "") {
        throw new Error("The name cannot be empty.");
      }

      this.setAttribute("name", name);

      if (this.#input !== undefined) {
        this.#input.name = name;
      }
    }

    get required(): boolean {
      return this.hasAttribute("required");
    }

    set required(required: boolean) {
      if (required) {
        this.setAttribute("required", "");
      } else {
        this.removeAttribute("required");
      }
    }
  }

  window.customElements.define("woltlab-core-label-picker", WoltlabCoreLabelPickerElement);
}
