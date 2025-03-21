import State, { StateChangeCause } from "./ListView/State";
import { trigger as triggerDomChange } from "../Dom/Change/Listener";
import { setInnerHtml } from "../Dom/Util";
import { getItems } from "../Api/ListViews/GetItems";

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

    this.#state = this.#setupState(viewId, pageNo, baseUrl, sortField, sortOrder);
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

    triggerDomChange();
  }
}
