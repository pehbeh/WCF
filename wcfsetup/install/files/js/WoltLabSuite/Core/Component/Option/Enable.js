define(["require", "exports", "WoltLabSuite/Core/Helper/Selector"], function (require, exports, Selector_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    function change(option) {
        const disableOptions = eval(option.dataset.disableOptions);
        const enableOptions = eval(option.dataset.enableOptions);
        if (option instanceof HTMLInputElement) {
            switch (option.type) {
                case "checkbox":
                    showOptions(option.checked, disableOptions, enableOptions);
                    break;
                case "radio":
                    if (option.checked) {
                        let isActive = true;
                        if (option.dataset.isBoolean && option.value != "1") {
                            isActive = false;
                        }
                        showOptions(isActive, disableOptions, enableOptions);
                    }
                    break;
            }
        }
        else if (option instanceof HTMLSelectElement) {
            const value = option.value;
            const relevantDisableOptions = disableOptions
                .filter((item) => item.value == value)
                .map((item) => item.option);
            const relevantEnableOptions = enableOptions
                .filter((item) => item.value == value)
                .map((item) => item.option);
            showOptions(true, relevantDisableOptions, relevantEnableOptions);
        }
    }
    function showOptions(isActive, disableOptions, enableOptions) {
        disableOptions.forEach((optionName) => {
            getOptionElements(optionName).forEach((element) => {
                enableOption(element, !isActive);
            });
        });
        enableOptions.forEach((optionName) => {
            getOptionElements(optionName).forEach((element) => {
                enableOption(element, isActive);
            });
        });
    }
    function enableOption(element, enable) {
        if (element instanceof HTMLSelectElement ||
            (element instanceof HTMLInputElement &&
                (element.type === "checkbox" || element.type === "file" || element.type === "radio"))) {
            if (element instanceof HTMLInputElement && element.type === "radio") {
                if (!element.checked) {
                    element.disabled = !enable;
                }
                else {
                    // Skip active radio buttons, this preserves the value on submit,
                    // while the user is still unable to move the selection to the other,
                    // now disabled options.
                }
            }
            else {
                element.disabled = !enable;
            }
            const parentOptionTypeBoolean = element.closest(".optionTypeBoolean");
            if (parentOptionTypeBoolean) {
                // escape dots so that they are not recognized as class selectors
                const elementId = element.id.replace(/\./g, "\\.");
                const noElement = document.getElementById(elementId + "_no");
                if (noElement)
                    noElement.disabled = !enable;
                const neverElement = document.getElementById(elementId + "_never");
                if (neverElement)
                    neverElement.disabled = !enable;
            }
        }
        else {
            if (enable)
                element.removeAttribute("readonly");
            else
                element.setAttribute("readonly", "true");
        }
        if (enable) {
            element.closest("dl")?.classList.remove("disabled");
        }
        else {
            element.closest("dl")?.classList.add("disabled");
        }
    }
    function getOptionElements(optionName) {
        const optionElement = document.getElementById(optionName);
        if (optionElement) {
            return [optionElement];
        }
        const container = document.querySelectorAll(`.${optionName}Input > dd`);
        return Array.from(container)
            .map((element) => {
            return Array.from(element.querySelectorAll("input, select, textarea"));
        })
            .flat();
    }
    function setup() {
        (0, Selector_1.wheneverFirstSeen)(".jsEnablesOptions", (element) => {
            change(element);
            element.addEventListener("change", () => {
                change(element);
            });
        });
    }
});
