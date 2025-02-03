import { getPhrase } from "WoltLabSuite/Core/Language";
import { getPageOverlayContainer } from "WoltLabSuite/Core/Helper/PageOverlay";

enum SnackbarType {
  Success,
  Progress,
}

// eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
class Snackbar extends EventTarget {
  #message: string = "";
  readonly #type: SnackbarType;
  #snackbarElement: HTMLElement;

  constructor(message: string, type: SnackbarType) {
    super();

    this.#message = message;
    this.#type = type;

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
    if (this.isProgress()) {
      message.setAttribute("aria-live", "polite");
    }
    message.append(this.message);

    const dismissButton = document.createElement("button");
    dismissButton.type = "button";
    dismissButton.classList.add("snackbar__dismissButton");
    dismissButton.setAttribute("aria-label", getPhrase("wcf.global.button.close"));
    dismissButton.addEventListener("click", () => {
      this.close();
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

  isVisible(): boolean {
    return this.#snackbarElement.parentElement !== null;
  }

  close(): void {
    if (!this.isVisible()) {
      return;
    }

    this.#snackbarElement.remove();

    this.dispatchEvent(new CustomEvent("close"));
  }

  get element(): HTMLElement {
    return this.#snackbarElement;
  }
}

interface SnackbarEventMap {
  close: CustomEvent<void>;
}

// eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
interface Snackbar extends EventTarget {
  addEventListener: {
    <T extends keyof SnackbarEventMap>(
      type: T,
      listener: (this: Snackbar, ev: SnackbarEventMap[T]) => any,
      options?: boolean | AddEventListenerOptions,
    ): void;
  } & HTMLElement["addEventListener"];
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
      oldSnackbar?.close();
    }

    this.#snackbars.push(snackbar);
    this.#element.prepend(snackbar.element);

    void snackbar.addEventListener("close", () => {
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
