import { getRow } from "../Api/Gridviews/GetRow";
import { getRows } from "../Api/Gridviews/GetRows";
import DomChangeListener from "../Dom/Change/Listener";
import DomUtil from "../Dom/Util";
import { wheneverFirstSeen } from "../Helper/Selector";
import UiDropdownSimple from "../Ui/Dropdown/Simple";
import { State, UpdateCause } from "./GridView/State";

export class GridView {
  readonly #gridClassName: string;
  readonly #table: HTMLTableElement;
  readonly #state: State;

  readonly #noItemsNotice: HTMLElement;

  #gridViewParameters?: Map<string, string>;

  constructor(
    gridId: string,
    gridClassName: string,
    pageNo: number,
    baseUrl: string = "",
    sortField = "",
    sortOrder = "ASC",
    gridViewParameters?: Map<string, string>,
  ) {
    this.#gridClassName = gridClassName;
    this.#table = document.getElementById(`${gridId}_table`) as HTMLTableElement;
    this.#noItemsNotice = document.getElementById(`${gridId}_noItemsNotice`) as HTMLElement;

    this.#gridViewParameters = gridViewParameters;

    this.#initInteractions();
    this.#state = this.#setupState(gridId, pageNo, baseUrl, sortField, sortOrder);
    this.#initEventListeners();
  }

  async #loadRows(source: UpdateCause): Promise<void> {
    const response = (
      await getRows(
        this.#gridClassName,
        this.#state.getPageNo(),
        this.#state.getSortField(),
        this.#state.getSortOrder(),
        this.#state.getActiveFilters(),
        this.#gridViewParameters,
      )
    ).unwrap();
    DomUtil.setInnerHtml(this.#table.querySelector("tbody")!, response.template);

    this.#table.hidden = response.totalRows == 0;
    this.#noItemsNotice.hidden = response.totalRows != 0;
    this.#state.updateFromResponse(source, response.pages, response.filterLabels);

    DomChangeListener.trigger();
  }

  async #refreshRow(row: HTMLElement): Promise<void> {
    const response = (await getRow(this.#gridClassName, row.dataset.objectId!)).unwrap();
    row.replaceWith(DomUtil.createFragmentFromHtml(response.template));
    DomChangeListener.trigger();
  }

  #initInteractions(): void {
    wheneverFirstSeen(`#${this.#table.id} tbody tr`, (row) => {
      row.querySelectorAll<HTMLElement>(".dropdownToggle").forEach((element) => {
        let dropdown = UiDropdownSimple.getDropdownMenu(element.dataset.target!);
        if (!dropdown) {
          dropdown = element.closest(".dropdown")!.querySelector<HTMLElement>(".dropdownMenu")!;
        }

        dropdown?.querySelectorAll<HTMLButtonElement>("[data-interaction]").forEach((element) => {
          element.addEventListener("click", () => {
            row.dispatchEvent(
              new CustomEvent("interaction", {
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
    this.#table.addEventListener("refresh", (event) => {
      void this.#refreshRow(event.target as HTMLElement);
    });

    this.#table.addEventListener("remove", (event) => {
      (event.target as HTMLElement).remove();
    });
  }

  #setupState(gridId: string, pageNo: number, baseUrl: string, sortField: string, sortOrder: string): State {
    const state = new State(gridId, this.#table, pageNo, baseUrl, sortField, sortOrder);
    state.addEventListener("change", (event) => {
      void this.#loadRows(event.detail.source);
    });

    return state;
  }
}
