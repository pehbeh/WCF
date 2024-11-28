/**
 * Sortable lists with optimized handling per device sizes.
 *
 * @author  Olaf Braun, Alexander Ebert
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @woltlabExcludeBundle tiny
 */

import * as Core from "../../Core";
import Sortable from "sortablejs";
import { apiOnce } from "WoltLabSuite/Core/Ajax";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { getPhrase } from "WoltLabSuite/Core/Language";

interface UnknownObject {
  [key: string]: unknown;
}

interface SortableListOptions {
  containerId: string;
  className: string;
  offset: number;
  options: Sortable.Options;
  isSimpleSorting: boolean;
  toleranceElement?: string;
  maxNestingLevel?: number;
  additionalParameters: UnknownObject;
}

function getNestingLevel(element: HTMLElement, container?: HTMLElement): number {
  let nestingLevel = 0;
  let sortableNode: HTMLElement | null = Sortable.utils.closest(element, ".sortableNode");

  while (
    sortableNode !== null &&
    (!container || (sortableNode !== container && sortableNode.parentNode !== container))
  ) {
    sortableNode = Sortable.utils.closest(sortableNode.parentElement!, ".sortableNode");
    nestingLevel++;
  }

  return nestingLevel;
}

class UiSortableList {
  protected readonly _options: SortableListOptions;
  readonly #container: HTMLElement | null;
  readonly #sortables = new Map<number, Sortable>();

  /**
   * Initializes the sortable list controller.
   */
  constructor(opts: Partial<SortableListOptions>) {
    this._options = Core.extend(
      {
        containerId: "",
        className: "",
        offset: 0,
        maxNestingLevel: undefined,
        toleranceElement: "span",
        options: {
          animation: 150,
          fallbackOnBody: true,
          dataIdAttr: "data-object-id",
          chosenClass: "sortablePlaceholder",
          ghostClass: "",
          draggable: "li",
          filter: (event: Event | TouchEvent, target: HTMLElement) => {
            if (Sortable.utils.is(target, ".sortableNoSorting")) {
              return true;
            }

            const eventTarget = event.target as HTMLElement;
            if (eventTarget === target) {
              return false;
            }
            if (eventTarget.parentElement === target) {
              return false;
            }
            if (!this._options.toleranceElement) {
              return true;
            }

            return !Sortable.utils.is(eventTarget, this._options.toleranceElement);
          },
          onMove: (event: Sortable.MoveEvent) => {
            if (this._options.maxNestingLevel === undefined) {
              return true;
            }

            const closest = Sortable.utils.closest(event.to, ".sortableNode");
            if (!closest) {
              // Top level
              return true;
            }

            if (closest && Sortable.utils.is(closest, ".sortableNoNesting")) {
              return false;
            }

            const levelOfDraggedNode = Math.max(
              ...Array.from(event.dragged.querySelectorAll(".sortableList")).map((list: HTMLElement) => {
                return getNestingLevel(list, event.dragged);
              }),
            );

            if (getNestingLevel(event.to) + levelOfDraggedNode > this._options.maxNestingLevel) {
              return false;
            }

            return true;
          },
          onEnd: (event: Sortable.SortableEvent) => {
            if (this._options.maxNestingLevel === undefined) {
              return;
            }

            event.to.querySelectorAll(".sortableNode").forEach((node: HTMLElement) => {
              if (getNestingLevel(node) > this._options.maxNestingLevel!) {
                node.classList.add("sortableNoNesting");
              } else {
                node.classList.remove("sortableNoNesting");
              }
            });
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
        this._options.options.draggable = "tr";
        this._options.toleranceElement = undefined;
      }

      this.#sortables.set(
        sortableList.dataset.objectId ? parseInt(sortableList.dataset.objectId, 10) : 0,
        new Sortable(sortableList, {
          direction: "vertical",
          ...this._options.options,
        }),
      );
    } else {
      this.#container.querySelectorAll(".sortableList").forEach((list: HTMLElement) => {
        this.#sortables.set(
          parseInt(list.dataset.objectId!, 10),
          new Sortable(list, {
            group: "nested",
            ...this._options.options,
          }),
        );
      });
    }

    if (this._options.className) {
      let formSubmit = this.#container.querySelector(".formSubmit");
      if (!formSubmit) {
        formSubmit = this.#container.nextElementSibling;
      }
      if (!formSubmit) {
        console.debug("[UiSortableList] Unable to find form submit for saving, aborting.");
        return;
      }

      formSubmit.querySelector('button[data-type="submit"]')?.addEventListener("click", () => {
        this.save();
      });
    }
  }

  public save(): void {
    if (!this._options.className) {
      return;
    }

    const structure = Object.fromEntries(
      Array.from(this.#sortables).map(([objectId, sortable]) => [objectId, sortable.toArray()]),
    );

    const parameters = Core.extend(
      {
        data: {
          offset: this._options.offset,
          structure: structure,
        },
      },
      this._options.additionalParameters,
    );

    apiOnce({
      data: {
        actionName: "updatePosition",
        className: this._options.className,
        interfaceName: "wcf\\data\\ISortableAction",
        parameters: parameters,
      },
      success: () => {
        showNotification(getPhrase("wcf.global.success.edit"));
      },
    });
  }
}

export = UiSortableList;
