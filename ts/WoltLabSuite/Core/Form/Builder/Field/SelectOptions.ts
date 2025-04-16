import { identify } from "WoltLabSuite/Core/Dom/Util";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { getValues, init as initI18n } from "WoltLabSuite/Core/Language/Input";
import Sortable from "sortablejs";

type Data = {
  key: string;
  value: Record<string, string>;
};

type Languages = Record<string, string>;

let _languages: Languages;

export function setup(formField: HTMLInputElement, languages: Languages): void {
  _languages = languages;

  const ul = createUi(formField);

  formField.form?.addEventListener("submit", () => {
    setHiddenValue(formField);
  });

  new Sortable(ul, {
    direction: "vertical",
    animation: 150,
    fallbackOnBody: true,
    draggable: "li",
    handle: ".selectOptionsListItem__handle",
  });
}

function createUi(formField: HTMLInputElement): HTMLUListElement {
  const ul = document.createElement("ul");
  ul.classList.add("selectOptionsList");
  formField.parentElement?.append(ul);

  if (formField.value) {
    const data = JSON.parse(formField.value) as Data[];
    data.forEach((option) => {
      createRow(ul, option);
    });
  } else {
    createRow(ul);
  }

  return ul;
}

function createRow(ul: HTMLUListElement, option?: Data, autoFocus: boolean = false): void {
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
      createRow(ul);
      createRow(ul, undefined, true);
    }
  });

  li.append(getSortableHandle(), addButton, deleteButton, keyInput, equalsIcon, valueInput);

  const hasI18nValues = option && !Object.hasOwn(option.value, 0);

  initI18n(identify(valueInput), hasI18nValues ? option.value : {}, _languages, false);

  if (!hasI18nValues) {
    valueInput.value = option?.value[0] ?? "";
  }

  if (autoFocus) {
    keyInput.focus();
  }
}

function getAddButton(): HTMLButtonElement {
  const addIcon = document.createElement("fa-icon");
  addIcon.setIcon("plus");

  const addButton = document.createElement("button");
  addButton.type = "button";
  addButton.append(addIcon);
  addButton.classList.add("jsTooltip");
  addButton.title = getPhrase("wcf.global.button.add");

  return addButton;
}

function getDeleteButton(): HTMLButtonElement {
  const deleteIcon = document.createElement("fa-icon");
  deleteIcon.setIcon("xmark");

  const deleteButton = document.createElement("button");
  deleteButton.type = "button";
  deleteButton.append(deleteIcon);
  deleteButton.classList.add("jsTooltip");
  deleteButton.title = getPhrase("wcf.global.button.delete");

  return deleteButton;
}

function getKeyInput(): HTMLInputElement {
  const keyInput = document.createElement("input");
  keyInput.classList.add("selectOptionsListItem__key");
  keyInput.placeholder = getPhrase("wcf.form.selectOptions.key");
  keyInput.type = "text";
  keyInput.required = true;

  return keyInput;
}

function getValueInput(): HTMLInputElement {
  const valueInput = document.createElement("input");
  valueInput.classList.add("selectOptionsListItem__value");
  valueInput.placeholder = getPhrase("wcf.form.selectOptions.value");
  valueInput.type = "text";
  valueInput.required = true;

  return valueInput;
}

function getSortableHandle(): HTMLElement {
  const icon = document.createElement("fa-icon");
  icon.setIcon("up-down");
  const handle = document.createElement("span");
  handle.append(icon);
  handle.classList.add("selectOptionsListItem__handle");

  return handle;
}

function setHiddenValue(formField: HTMLInputElement): void {
  const data: Data[] = [];

  formField.parentElement?.querySelectorAll(".selectOptionsListItem").forEach((li) => {
    const key = li.querySelector<HTMLInputElement>(".selectOptionsListItem__key")!.value;
    const valueInput = li.querySelector<HTMLInputElement>(".selectOptionsListItem__value")!;

    data.push({
      key,
      value: Object.fromEntries(getValues(valueInput.id)),
    });
  });

  formField.value = JSON.stringify(data);
}
