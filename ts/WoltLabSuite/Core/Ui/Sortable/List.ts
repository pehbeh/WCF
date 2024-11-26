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
          chosenClass: "sortablePlaceholder",
          fallbackOnBody: true,
          swapThreshold: 0.65,
          filter: (event: Event | TouchEvent, target: HTMLElement, sortable: Sortable) => {
            //TODO
            console.log(event, target, sortable);
            return true;
          },
        },
        isSimpleSorting: false,
        additionalParameters: {},
      },
      opts,
    ) as SortableListOptions;

    this.#container = document.getElementById(this._options.containerId);
    if (!this.#container) {
      throw new Error(`Container '${this._options.containerId}' not found.`);
    }

    new Sortable(this.#container.querySelector(".sortableList")!, this._options.options);
  }
}

export = UiSortableList;
