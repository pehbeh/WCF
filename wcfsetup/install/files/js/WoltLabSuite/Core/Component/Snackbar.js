define(["require", "exports", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Helper/PageOverlay"], function (require, exports, Language_1, PageOverlay_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.showSuccessSnackbar = showSuccessSnackbar;
    exports.showProgressSnackbar = showProgressSnackbar;
    exports.showDefaultSuccessSnackbar = showDefaultSuccessSnackbar;
    var SnackbarType;
    (function (SnackbarType) {
        SnackbarType[SnackbarType["Success"] = 0] = "Success";
        SnackbarType[SnackbarType["Progress"] = 1] = "Progress";
    })(SnackbarType || (SnackbarType = {}));
    // eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
    class Snackbar extends EventTarget {
        #message = "";
        #type;
        #snackbarElement;
        constructor(message, type) {
            super();
            this.#message = message;
            this.#type = type;
            this.#render();
        }
        get message() {
            return this.#message;
        }
        set message(message) {
            this.#message = message;
            this.#snackbarElement.querySelector(".snackbar__message").textContent = message;
        }
        markAsDone(message) {
            this.message = message;
            const iconWrapper = this.#snackbarElement.querySelector(".snackbar__icon");
            iconWrapper.classList.remove("snackbar__icon--progress");
            iconWrapper.classList.add("snackbar__icon--success");
            const icon = iconWrapper.querySelector("fa-icon");
            icon.setIcon("check");
            this.#setHideTimeout();
        }
        #render() {
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
            dismissButton.setAttribute("aria-label", (0, Language_1.getPhrase)("wcf.global.button.close"));
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
        #setHideTimeout() {
            window.setTimeout(() => {
                this.close();
            }, 3_000);
        }
        isProgress() {
            return this.#type == SnackbarType.Progress;
        }
        isVisible() {
            if (this.#snackbarElement.parentElement === null) {
                return false;
            }
            if (this.#snackbarElement.classList.contains("snackbar--closing")) {
                return false;
            }
            return true;
        }
        close() {
            if (!this.isVisible()) {
                return;
            }
            this.dispatchEvent(new CustomEvent("close"));
            // The animation to move the element vertically relative to its height
            // requires the value to be computed first.
            const height = Math.trunc(getSnackbarContainer().getGapValue() + this.#snackbarElement.getBoundingClientRect().height);
            this.#snackbarElement.style.setProperty("--height", `${height}px`);
            this.#snackbarElement.classList.add("snackbar--closing");
            this.#snackbarElement.addEventListener("animationend", () => {
                this.#snackbarElement.remove();
            });
        }
        get element() {
            return this.#snackbarElement;
        }
    }
    let snackbarContainer;
    function getSnackbarContainer() {
        if (!snackbarContainer) {
            snackbarContainer = new SnackbarContainer();
        }
        return snackbarContainer;
    }
    class SnackbarContainer {
        #element;
        #snackbars = [];
        constructor() {
            this.#element = document.createElement("div");
            this.#element.classList.add("snackbarContainer");
            (0, PageOverlay_1.getPageOverlayContainer)().append(this.#element);
        }
        addSnackbar(snackbar) {
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
        getGapValue() {
            const gap = window.getComputedStyle(this.#element).gap;
            const match = gap.match(/^(\d+)px$/);
            if (match === null) {
                return 0;
            }
            return parseInt(match[1]);
        }
    }
    function showSuccessSnackbar(message) {
        return new Snackbar(message, SnackbarType.Success);
    }
    function showProgressSnackbar(message) {
        return new Snackbar(message, SnackbarType.Progress);
    }
    function showDefaultSuccessSnackbar() {
        return showSuccessSnackbar((0, Language_1.getPhrase)("wcf.global.success"));
    }
});
