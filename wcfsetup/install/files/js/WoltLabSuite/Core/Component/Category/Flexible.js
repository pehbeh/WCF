define(["require", "exports", "../../Core"], function (require, exports, Core_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.FlexibleCategoryList = void 0;
    class FlexibleCategoryList {
        #list;
        #categories = new Map();
        #parentCategories = new Map();
        constructor(elementID) {
            this.#list = document.getElementById(elementID);
            // No nested categories
            if (!this.#list.querySelector("li li")) {
                this.#list.classList.add("flexibleCategoryListDisabled");
                return;
            }
            this.#buildStructure();
            this.#list.querySelectorAll("input:checked").forEach((input) => {
                (0, Core_1.triggerEvent)(input, "change");
            });
        }
        #buildStructure() {
            this.#list.querySelectorAll(".jsCategory").forEach((category) => {
                category.addEventListener("change", () => {
                    this.#updateSelection(category);
                });
                this.#categories.set(category, []);
                category
                    .closest("li")
                    .querySelectorAll(".jsChildCategory")
                    .forEach((childCategory) => {
                    this.#categories.get(category).push(childCategory);
                    this.#categories.set(childCategory, []);
                    this.#parentCategories.set(childCategory, category);
                    childCategory.addEventListener("change", () => {
                        this.#updateSelection(childCategory);
                    });
                    childCategory
                        .closest("li")
                        .querySelectorAll(".jsSubChildCategory")
                        .forEach((subChildCategory) => {
                        this.#categories.get(childCategory).push(subChildCategory);
                        this.#parentCategories.set(subChildCategory, childCategory);
                        subChildCategory.addEventListener("change", () => {
                            this.#updateSelection(subChildCategory);
                        });
                    });
                });
            });
        }
        #updateSelection(category) {
            const parentCategory = this.#parentCategories.get(category);
            if (category.checked) {
                if (parentCategory) {
                    parentCategory.checked = true;
                    this.#updateSelection(parentCategory);
                }
            }
            else {
                // uncheck child categories
                this.#categories.get(category)?.forEach((childCategory) => {
                    childCategory.checked = false;
                    this.#updateSelection(childCategory);
                });
            }
        }
    }
    exports.FlexibleCategoryList = FlexibleCategoryList;
});
