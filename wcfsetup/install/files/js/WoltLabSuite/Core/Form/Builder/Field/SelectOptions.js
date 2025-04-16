define(["require", "exports", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Language/Input"], function (require, exports, Util_1, Language_1, Input_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    let _languages;
    function setup(formField, languages) {
        _languages = languages;
        createUi(formField);
        formField.form?.addEventListener("submit", () => {
            setHiddenValue(formField);
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
            return;
        }
        createRow(ul);
    }
    function createRow(ul, option) {
        const li = document.createElement("li");
        li.classList.add("selectOptionsListItem");
        ul.append(li);
        const addButton = getAddButton();
        addButton.addEventListener("click", () => {
            createRow(ul);
        });
        const deleteButton = getDeleteButton();
        deleteButton.addEventListener("click", () => {
            li.remove();
            if (!ul.childElementCount) {
                createRow(ul);
            }
        });
        const keyInput = getKeyInput();
        keyInput.value = option ? option.key : "";
        const equalsIcon = document.createElement("fa-icon");
        equalsIcon.setIcon("equals");
        const valueInput = getValueInput();
        li.append(addButton, deleteButton, keyInput, equalsIcon, valueInput);
        const hasI18nValues = option && !Object.hasOwn(option.value, 0);
        (0, Input_1.init)((0, Util_1.identify)(valueInput), hasI18nValues ? option.value : {}, _languages, false);
        if (!hasI18nValues) {
            valueInput.value = option?.value[0] ?? "";
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
