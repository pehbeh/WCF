import { getPhrase } from "WoltLabSuite/Core/Language";
import { getPageOverlayContainer } from "WoltLabSuite/Core/Helper/PageOverlay";

enum SnackbarType {
  Success,
  Progress,
}

class Snackbar {
  #message: string = "";
  readonly #type: SnackbarType;
  #snackbarElement: HTMLElement;
  readonly #hidden: Promise<void>;
  #hiddenResolve: () => void;

  constructor(message: string, type: SnackbarType) {
    this.#message = message;
    this.#type = type;

    this.#hidden = new Promise<void>((resolve) => {
      this.#hiddenResolve = resolve;
    });
    this.#render();
  }

  get message(): string {
    return this.#message;
  }

  set message(message: string) {
    this.#message = message;

    this.#snackbarElement.querySelector(".snackbar__message")!.textContent = message;
  }

  markAsDone(message: string): void {
    this.message = message;

    const iconWrapper = this.#snackbarElement.querySelector(".snackbar__icon")!;
    iconWrapper.classList.remove("snackbar__icon--progress");
    iconWrapper.classList.add("snackbar__icon--success");

    const icon = iconWrapper.querySelector("fa-icon")!;
    icon.setIcon("check");

    this.#setHideTimeout();
  }

  #render(): void {
    const iconWrapper = document.createElement("div");
    iconWrapper.classList.add("snackbar__icon");
    iconWrapper.classList.add(this.isProgress() ? "snackbar__icon--progress" : "snackbar__icon--success");

    const icon = document.createElement("fa-icon");
    icon.size = 24;
    icon.setIcon(this.isProgress() ? "spinner" : "check");
    iconWrapper.append(icon);

    const message = document.createElement("div");
    message.classList.add("snackbar__message");
    message.append(this.message);

    const dismissButton = document.createElement("button");
    dismissButton.type = "button";
    dismissButton.classList.add("snackbar__dismissButton");
    dismissButton.setAttribute("aria-label", getPhrase("wcf.global.button.close"));
    dismissButton.addEventListener("click", () => {
      this.hide();
    });

    const dismissIcon = document.createElement("fa-icon");
    dismissIcon.size = 24;
    dismissIcon.setIcon("xmark");
    dismissButton.append(dismissIcon);

    this.#snackbarElement = document.createElement("div");
    this.#snackbarElement.classList.add("snackbar");
    this.#snackbarElement.setAttribute("role", "status");
    this.#snackbarElement.append(iconWrapper, message, dismissButton);

    getSnackbarContainer().addSnackbar(this);

    if (!this.isProgress()) {
      this.#setHideTimeout();
    }
  }

  #setHideTimeout(): void {
    window.setTimeout(() => {
      //this.hide();
    }, 5000);
  }

  isProgress(): boolean {
    return this.#type == SnackbarType.Progress;
  }

  hide(): void {
    this.#snackbarElement.remove();
    this.#hiddenResolve();
  }

  get hidden(): Promise<void> {
    return this.#hidden;
  }

  get element(): HTMLElement {
    return this.#snackbarElement;
  }
}

let snackbarContainer: SnackbarContainer;

function getSnackbarContainer(): SnackbarContainer {
  if (!snackbarContainer) {
    snackbarContainer = new SnackbarContainer();
  }

  return snackbarContainer;
}

class SnackbarContainer {
  readonly #element: HTMLElement;
  #snackbars: Snackbar[] = [];

  constructor() {
    this.#element = document.createElement("div");
    this.#element.classList.add("snackbarContainer");
    getPageOverlayContainer().append(this.#element);
  }

  addSnackbar(snackbar: Snackbar): void {
    if (this.#snackbars.length > 2) {
      const oldSnackbar = this.#snackbars.shift();
      oldSnackbar?.hide();
    }

    this.#snackbars.push(snackbar);
    this.#element.prepend(snackbar.element);

    void snackbar.hidden.then(() => {
      const i = this.#snackbars.indexOf(snackbar);
      if (i !== -1) {
        this.#snackbars = this.#snackbars.splice(i, 1);
      }
    });
  }
}

export function showSuccessSnackbar(message: string): Snackbar {
  return new Snackbar(message, SnackbarType.Success);
}

export function showProgressSnackbar(message: string): Snackbar {
  return new Snackbar(message, SnackbarType.Progress);
}

export function showDefaultSuccessSnackbar(): Snackbar {
  return showSuccessSnackbar(getPhrase("wcf.global.success"));
}
