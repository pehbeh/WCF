import State, { StateChangeCause } from "./ListView/State";
import { trigger as triggerDomChange } from "../Dom/Change/Listener";
import { setInnerHtml, createFragmentFromHtml } from "../Dom/Util";
import { getItems } from "../Api/ListViews/GetItems";
import { element as scrollToElement } from "WoltLabSuite/Core/Ui/Scroll";
import { wheneverFirstSeen } from "../Helper/Selector";
import UiDropdownSimple from "../Ui/Dropdown/Simple";
import { getItem } from "../Api/ListViews/GetItem";

export class ListView {
  readonly #viewClassName: string;
  readonly #viewElement: HTMLElement;
  readonly #state: State;
  readonly #noItemsNotice: HTMLElement;

  constructor(
    viewId: string,
    viewClassName: string,
    pageNo: number,
    baseUrl: string = "",
    sortField = "",
    sortOrder = "ASC",
  ) {
    this.#viewClassName = viewClassName;
    this.#viewElement = document.getElementById(`${viewId}_items`) as HTMLTableElement;
    this.#noItemsNotice = document.getElementById(`${viewId}_noItemsNotice`) as HTMLElement;

    this.#initInteractions();
    this.#state = this.#setupState(viewId, pageNo, baseUrl, sortField, sortOrder);
    this.#initEventListeners();
  }

  #setupState(viewId: string, pageNo: number, baseUrl: string, sortField: string, sortOrder: string): State {
    const state = new State(viewId, this.#viewElement, pageNo, baseUrl, sortField, sortOrder);
    state.addEventListener("list-view:change", (event) => {
      void this.#loadItems(event.detail.source);
    });
    /*state.addEventListener("grid-view:get-bulk-interactions", (event) => {
      void this.#loadBulkInteractions(event.detail.objectIds);
    });*/

    return state;
  }

  async #loadItems(cause: StateChangeCause): Promise<void> {
    const response = (
      await getItems(
        this.#viewClassName,
        this.#state.getPageNo(),
        this.#state.getSortField(),
        this.#state.getSortOrder(),
        this.#state.getActiveFilters(),
        //this.#gridViewParameters,
      )
    ).unwrap();
    setInnerHtml(this.#viewElement, response.template);

    this.#viewElement.hidden = response.totalItems === 0;
    this.#noItemsNotice.hidden = response.totalItems !== 0;
    this.#state.updateFromResponse(cause, response.pages, response.filterLabels);
    if (cause === StateChangeCause.Pagination) {
      scrollToElement(this.#viewElement);
    }

    triggerDomChange();
  }

  async #refreshItem(item: HTMLElement): Promise<void> {
    const response = (
      await getItem(this.#viewClassName, item.dataset.objectId! /*, this.#gridViewParameters*/)
    ).unwrap();
    item.replaceWith(createFragmentFromHtml(response.template));
    this.#state.refreshSelection();
    triggerDomChange();
  }

  #initInteractions(): void {
    wheneverFirstSeen(`#${this.#viewElement.id} .listView__item`, (item) => {
      item.querySelectorAll<HTMLElement>(".dropdownToggle").forEach((element) => {
        let dropdown = UiDropdownSimple.getDropdownMenu(element.dataset.target!);
        if (!dropdown) {
          dropdown = element.closest(".dropdown")!.querySelector<HTMLElement>(".dropdownMenu")!;
        }

        dropdown?.querySelectorAll<HTMLButtonElement>("[data-interaction]").forEach((element) => {
          element.addEventListener("click", () => {
            item.dispatchEvent(
              new CustomEvent("interaction:execute", {
                detail: element.dataset,
                bubbles: true,
              }),
            );
          });
        });
      });
    });
  }

  #initEventListeners(): void {
    this.#viewElement.addEventListener("interaction:invalidate-all", () => {
      void this.#loadItems(StateChangeCause.Change);
    });

    this.#viewElement.addEventListener("interaction:invalidate", (event) => {
      void this.#refreshItem(event.target as HTMLElement);
    });

    this.#viewElement.addEventListener("interaction:remove", (event) => {
      (event.target as HTMLElement).remove();
      //  this.#checkEmptyTable();
    });

    this.#viewElement.addEventListener("interaction:reset-selection", () => {
      this.#state.resetSelection();
    });
  }
}
