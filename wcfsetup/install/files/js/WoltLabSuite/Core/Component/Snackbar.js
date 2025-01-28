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
    class Snackbar {
        #message = "";
        #type;
        #snackbarElement;
        #hidden;
        #hiddenResolve;
        constructor(message, type) {
            this.#message = message;
            this.#type = type;
            this.#hidden = new Promise((resolve) => {
                this.#hiddenResolve = resolve;
            });
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
            message.append(this.message);
            const dismissButton = document.createElement("button");
            dismissButton.type = "button";
            dismissButton.classList.add("snackbar__dismissButton");
            dismissButton.setAttribute("aria-label", (0, Language_1.getPhrase)("wcf.global.button.close"));
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
        #setHideTimeout() {
            window.setTimeout(() => {
                //this.hide();
            }, 5000);
        }
        isProgress() {
            return this.#type == SnackbarType.Progress;
        }
        hide() {
            this.#snackbarElement.remove();
            this.#hiddenResolve();
        }
        get hidden() {
            return this.#hidden;
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
