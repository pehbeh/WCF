/**
 * Sortable lists with optimized handling per device sizes.
 *
 * @author  Alexander Ebert
 * @copyright  2001-2019 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @woltlabExcludeBundle tiny
 */

import * as Core from "../../Core";
import Sortable from "sortablejs";

interface UnknownObject {
  [key: string]: unknown;
}

interface SortableListOptions {
  containerId: string;
  className: string;
  offset: number;
  options: Sortable.Options;
  isSimpleSorting: boolean;
  toleranceElement: string;
  additionalParameters: UnknownObject;
}

class UiSortableList {
  protected readonly _options: SortableListOptions;
  readonly #container: HTMLElement | null;

  /**
   * Initializes the sortable list controller.
   */
  constructor(opts: Partial<SortableListOptions>) {
    this._options = Core.extend(
      {
        containerId: "",
        className: "",
        offset: 0,
        options: {
          animation: 150,
          swapThreshold: 0.65,
          fallbackOnBody: true,
          chosenClass: "sortablePlaceholder",
          ghostClass: "",
          draggable: "li:not(.sortableNoSorting)",
          toleranceElement: "span",
          filter: (event: Event | TouchEvent, target: HTMLElement) => {
            const eventTarget = event.target as HTMLElement;
            if (eventTarget === target) {
              return false;
            }
            if (eventTarget.parentElement !== target) {
              return false;
            }

            return eventTarget.nodeName !== this._options.toleranceElement;
          },
        } as Sortable.Options,
        isSimpleSorting: false,
        additionalParameters: {},
      },
      opts,
    ) as SortableListOptions;

    this.#container = document.getElementById(this._options.containerId);
    if (!this.#container) {
      throw new Error(`Container '${this._options.containerId}' not found.`);
    }

    if (this._options.isSimpleSorting) {
      const sortableList = this.#container.querySelector<HTMLElement>(".sortableList")!;
      if (sortableList.nodeName === "TBODY") {
        this._options.options.draggable = "tr:not(.sortableNoSorting)";
      }

      new Sortable(sortableList, {
        direction: "vertical",
        ...this._options.options,
      });
    } else {
      this.#container.querySelectorAll(".sortableList").forEach((list: HTMLElement) => {
        new Sortable(list, {
          group: "nested",
          ...this._options.options,
        });
      });
    }
  }
}

export = UiSortableList;
