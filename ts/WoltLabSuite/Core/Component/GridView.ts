import { getRow } from "../Api/Gridviews/GetRow";
import { getRows } from "../Api/Gridviews/GetRows";
import DomChangeListener from "../Dom/Change/Listener";
import DomUtil from "../Dom/Util";
import { wheneverFirstSeen } from "../Helper/Selector";
import UiDropdownSimple from "../Ui/Dropdown/Simple";
import Filter from "./GridView/Filter";

export class GridView {
  readonly #filter: Filter;
  readonly #gridClassName: string;
  readonly #table: HTMLTableElement;
  readonly #pagination: WoltlabCorePaginationElement;
  readonly #baseUrl: string;
  readonly #noItemsNotice: HTMLElement;
  #pageNo: number;
  #sortField: string;
  #sortOrder: string;
  #defaultSortField: string;
  #defaultSortOrder: string;
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
    this.#pagination = document.getElementById(`${gridId}_pagination`) as WoltlabCorePaginationElement;
    this.#noItemsNotice = document.getElementById(`${gridId}_noItemsNotice`) as HTMLElement;
    this.#pageNo = pageNo;
    this.#baseUrl = baseUrl;
    this.#sortField = sortField;
    this.#defaultSortField = sortField;
    this.#sortOrder = sortOrder;
    this.#defaultSortOrder = sortOrder;
    this.#gridViewParameters = gridViewParameters;

    this.#initPagination();
    this.#initSorting();
    this.#initInteractions();
    this.#filter = this.#setupFilter(gridId);
    this.#initEventListeners();

    window.addEventListener("popstate", () => {
      this.#handlePopState();
    });
  }

  #initPagination(): void {
    this.#pagination.addEventListener("switchPage", (event: CustomEvent) => {
      void this.#switchPage(event.detail);
    });
  }

  #initSorting(): void {
    this.#table
      .querySelectorAll<HTMLTableCellElement>('.gridView__headerColumn[data-sortable="1"]')
      .forEach((element) => {
        const button = element.querySelector<HTMLButtonElement>(".gridView__headerColumn__button");
        button?.addEventListener("click", () => {
          this.#sort(element.dataset.id!);
        });
      });

    this.#renderActiveSorting();
  }

  #sort(sortField: string): void {
    if (this.#sortField == sortField && this.#sortOrder == "ASC") {
      this.#sortOrder = "DESC";
    } else {
      this.#sortField = sortField;
      this.#sortOrder = "ASC";
    }

    this.#switchPage(1);
    this.#renderActiveSorting();
  }

  #renderActiveSorting(): void {
    this.#table.querySelectorAll<HTMLTableCellElement>('th[data-sortable="1"]').forEach((element) => {
      element.classList.remove("active", "ASC", "DESC");

      if (element.dataset.id == this.#sortField) {
        element.classList.add("active", this.#sortOrder);
      }
    });
  }

  #switchPage(pageNo: number, updateQueryString: boolean = true): void {
    this.#pagination.page = pageNo;
    this.#pageNo = pageNo;

    void this.#loadRows(updateQueryString);
  }

  async #loadRows(updateQueryString: boolean = true): Promise<void> {
    const response = (
      await getRows(
        this.#gridClassName,
        this.#pageNo,
        this.#sortField,
        this.#sortOrder,
        this.#filter.getActiveFilters(),
        this.#gridViewParameters,
      )
    ).unwrap();
    DomUtil.setInnerHtml(this.#table.querySelector("tbody")!, response.template);

    this.#table.hidden = response.totalRows == 0;
    this.#noItemsNotice.hidden = response.totalRows != 0;
    this.#pagination.count = response.pages;

    if (updateQueryString) {
      this.#updateQueryString();
    }

    DomChangeListener.trigger();

    this.#filter.setFilterLabels(response.filterLabels);
  }

  async #refreshRow(row: HTMLElement): Promise<void> {
    const response = (await getRow(this.#gridClassName, row.dataset.objectId!)).unwrap();
    row.replaceWith(DomUtil.createFragmentFromHtml(response.template));
    DomChangeListener.trigger();
  }

  #updateQueryString(): void {
    if (!this.#baseUrl) {
      return;
    }

    const url = new URL(this.#baseUrl);

    const parameters: [string, string][] = [];
    if (this.#pageNo > 1) {
      parameters.push(["pageNo", this.#pageNo.toString()]);
    }
    if (this.#sortField) {
      parameters.push(["sortField", this.#sortField]);
      parameters.push(["sortOrder", this.#sortOrder]);
    }

    this.#filter.getActiveFilters().forEach((value, key) => {
      parameters.push([`filters[${key}]`, value]);
    });

    if (parameters.length > 0) {
      url.search += url.search !== "" ? "&" : "?";
      url.search += new URLSearchParams(parameters).toString();
    }

    window.history.pushState({}, document.title, url.toString());
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

  #handlePopState(): void {
    let pageNo = 1;
    this.#sortField = this.#defaultSortField;
    this.#sortOrder = this.#defaultSortOrder;
    this.#filter.resetFilters();

    const url = new URL(window.location.href);
    url.searchParams.forEach((value, key) => {
      if (key === "pageNo") {
        pageNo = parseInt(value, 10);
        return;
      }

      if (key === "sortField") {
        this.#sortField = value;
      }

      if (key === "sortOrder") {
        this.#sortOrder = value;
      }

      const matches = key.match(/^filters\[([a-z0-9_]+)\]$/i);
      if (matches) {
        this.#filter.setFilter(matches[1], value);
      }
    });

    this.#switchPage(pageNo, false);
  }

  #initEventListeners(): void {
    this.#table.addEventListener("refresh", (event) => {
      void this.#refreshRow(event.target as HTMLElement);
    });

    this.#table.addEventListener("remove", (event) => {
      (event.target as HTMLElement).remove();
    });
  }

  #setupFilter(gridId: string): Filter {
    const filter = new Filter(gridId);
    filter.addEventListener("switchPage", (event) => {
      this.#switchPage(event.detail.pageNo);
    });

    return filter;
  }
}
