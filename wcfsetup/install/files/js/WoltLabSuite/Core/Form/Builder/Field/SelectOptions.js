define(["require", "exports", "tslib", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Language/Input", "sortablejs"], function (require, exports, tslib_1, Util_1, Language_1, Input_1, sortablejs_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    sortablejs_1 = tslib_1.__importDefault(sortablejs_1);
    let _languages;
    function setup(formField, languages) {
        _languages = languages;
        const ul = createUi(formField);
        formField.form?.addEventListener("submit", () => {
            setHiddenValue(formField);
        });
        new sortablejs_1.default(ul, {
            direction: "vertical",
            animation: 150,
            fallbackOnBody: true,
            draggable: "li",
            handle: ".selectOptionsListItem__handle",
        });
    }
    function createUi(formField) {
        const ul = document.createElement("ul");
        ul.classList.add("selectOptionsList");
        formField.parentElement?.append(ul);
        if (formField.value) {
            const data = JSON.parse(formField.value);
            data.forEach((option) => {
                createRow(ul, option);
            });
        }
        else {
            createRow(ul);
        }
        return ul;
    }
    function createRow(ul, option, autoFocus = false) {
        const li = document.createElement("li");
        li.classList.add("selectOptionsListItem");
        ul.append(li);
        const addButton = getAddButton();
        addButton.addEventListener("click", () => {
            createRow(ul, undefined, true);
        });
        const deleteButton = getDeleteButton();
        deleteButton.addEventListener("click", () => {
            li.remove();
            if (!ul.childElementCount) {
                createRow(ul);
            }
        });
        const keyInput = getKeyInput();
        keyInput.addEventListener("keydown", (event) => {
            if (event.key === "Enter") {
                event.preventDefault();
                createRow(ul, undefined, true);
            }
        });
        keyInput.value = option ? option.key : "";
        const equalsIcon = document.createElement("fa-icon");
        equalsIcon.setIcon("equals");
        const valueInput = getValueInput();
        valueInput.addEventListener("keydown", (event) => {
            if (event.key === "Enter") {
                event.preventDefault();
                createRow(ul, undefined, true);
            }
        });
        li.append(getSortableHandle(), addButton, deleteButton, keyInput, equalsIcon, valueInput);
        const hasI18nValues = option && !Object.hasOwn(option.value, 0);
        (0, Input_1.init)((0, Util_1.identify)(valueInput), hasI18nValues ? option.value : {}, _languages, false);
        if (!hasI18nValues) {
            valueInput.value = option?.value[0] ?? "";
        }
        if (autoFocus) {
            keyInput.focus();
        }
    }
    function getAddButton() {
        const addIcon = document.createElement("fa-icon");
        addIcon.setIcon("plus");
        const addButton = document.createElement("button");
        addButton.type = "button";
        addButton.append(addIcon);
        addButton.classList.add("jsTooltip");
        addButton.title = (0, Language_1.getPhrase)("wcf.global.button.add");
        return addButton;
    }
    function getDeleteButton() {
        const deleteIcon = document.createElement("fa-icon");
        deleteIcon.setIcon("xmark");
        const deleteButton = document.createElement("button");
        deleteButton.type = "button";
        deleteButton.append(deleteIcon);
        deleteButton.classList.add("jsTooltip");
        deleteButton.title = (0, Language_1.getPhrase)("wcf.global.button.delete");
        return deleteButton;
    }
    function getKeyInput() {
        const keyInput = document.createElement("input");
        keyInput.classList.add("selectOptionsListItem__key");
        keyInput.placeholder = (0, Language_1.getPhrase)("wcf.form.selectOptions.key");
        keyInput.type = "text";
        keyInput.required = true;
        return keyInput;
    }
    function getValueInput() {
        const valueInput = document.createElement("input");
        valueInput.classList.add("selectOptionsListItem__value");
        valueInput.placeholder = (0, Language_1.getPhrase)("wcf.form.selectOptions.value");
        valueInput.type = "text";
        valueInput.required = true;
        return valueInput;
    }
    function getSortableHandle() {
        const icon = document.createElement("fa-icon");
        icon.setIcon("up-down");
        const handle = document.createElement("span");
        handle.append(icon);
        handle.classList.add("selectOptionsListItem__handle");
        return handle;
    }
    function setHiddenValue(formField) {
        const data = [];
        formField.parentElement?.querySelectorAll(".selectOptionsListItem").forEach((li) => {
            const key = li.querySelector(".selectOptionsListItem__key").value;
            const valueInput = li.querySelector(".selectOptionsListItem__value");
            data.push({
                key,
                value: Object.fromEntries((0, Input_1.getValues)(valueInput.id)),
            });
        });
        formField.value = JSON.stringify(data);
    }
});
