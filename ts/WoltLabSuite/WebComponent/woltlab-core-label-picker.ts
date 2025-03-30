{
  class WoltlabCoreLabelPickerElement extends HTMLElement {
    #labels: Map<number, string> | undefined = undefined;
    #name: string = "";
    #selected: number = 0;

    connectedCallback() {
      this.#setupLabels();

      this.innerHTML = "";

      const selected = parseInt(this.getAttribute("selected")!);
      this.removeAttribute("selected");
      if (!Number.isNaN(selected)) {
        this.selected = selected;
      }

      const name = this.getAttribute("name")!;
      this.removeAttribute("name");
      if (this.#name === "") {
        this.#name = name || "labelIDs[]";
      }

      this.classList.add("dropdown");

      const button = document.createElement("button");
      button.type = "button";
      button.classList.add("dropdownToggle");
      button.append(this.#getLabel(this.selected));

      const dropdownMenu = document.createElement("ol");
      dropdownMenu.classList.add("dropdownMenu");

      const input = document.createElement("input");
      input.type = "hidden";
      input.name = this.name;
      input.value = "0";

      for (const [labelId, html] of this.#labels!) {
        const button2 = document.createElement("button");
        button2.type = "button";
        button2.innerHTML = html;
        button2.addEventListener("click", () => {
          this.selected = labelId;

          button.innerHTML = "";
          button.append(this.#getLabel(this.selected));

          input.value = this.#selected.toString();
        });

        const listItem = document.createElement("li");
        listItem.append(button2);
        dropdownMenu.append(listItem);
      }

      this.append(button, dropdownMenu, input);
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

    get selected(): number {
      return this.#selected;
    }

    set selected(selected: number) {
      if (this.#labels?.has(selected)) {
        this.#selected = selected;

        // TODO: update the button label
      }
    }

    get name(): string {
      return this.#name;
    }
  }

  window.customElements.define("woltlab-core-label-picker", WoltlabCoreLabelPickerElement);
}
