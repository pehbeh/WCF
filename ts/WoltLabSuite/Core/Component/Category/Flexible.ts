/**
 * Handles selection of categories.
 *
 * @author Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
import { triggerEvent } from "../../Core";

export class FlexibleCategoryList {
  readonly #list: HTMLElement;
  readonly #categories = new Map<HTMLInputElement, HTMLInputElement[]>();
  readonly #parentCategories = new Map<HTMLInputElement, HTMLInputElement>();

  constructor(elementID: string) {
    this.#list = document.getElementById(elementID)!;

    // No nested categories
    if (!this.#list.querySelector("li li")) {
      this.#list.classList.add("flexibleCategoryListDisabled");
      return;
    }

    this.#buildStructure();

    this.#list.querySelectorAll("input:checked").forEach((input: HTMLInputElement) => {
      triggerEvent(input, "change");
    });
  }

  #buildStructure() {
    this.#list.querySelectorAll(".jsCategory").forEach((category: HTMLInputElement) => {
      category.addEventListener("change", () => {
        this.#updateSelection(category);
      });
      this.#categories.set(category, []);

      category
        .closest("li")!
        .querySelectorAll<HTMLInputElement>(".jsChildCategory")
        .forEach((childCategory) => {
          this.#categories.get(category)!.push(childCategory);
          this.#categories.set(childCategory, []);
          this.#parentCategories.set(childCategory, category);

          childCategory.addEventListener("change", () => {
            this.#updateSelection(childCategory);
          });

          childCategory
            .closest("li")!
            .querySelectorAll<HTMLInputElement>(".jsSubChildCategory")
            .forEach((subChildCategory) => {
              this.#categories.get(childCategory)!.push(subChildCategory);
              this.#parentCategories.set(subChildCategory, childCategory);

              subChildCategory.addEventListener("change", () => {
                this.#updateSelection(subChildCategory);
              });
            });
        });
    });
  }

  #updateSelection(category: HTMLInputElement) {
    const parentCategory = this.#parentCategories.get(category);

    if (category.checked) {
      if (parentCategory) {
        parentCategory.checked = true;

        this.#updateSelection(parentCategory);
      }
    } else {
      // uncheck child categories
      this.#categories.get(category)?.forEach((childCategory) => {
        childCategory.checked = false;

        this.#updateSelection(childCategory);
      });
    }
  }
}
