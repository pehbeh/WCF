import { getRow } from "../Api/Gridviews/GetRow";
import { getRows } from "../Api/Gridviews/GetRows";
import DomChangeListener from "../Dom/Change/Listener";
import DomUtil from "../Dom/Util";
import { promiseMutex } from "../Helper/PromiseMutex";
import UiDropdownSimple from "../Ui/Dropdown/Simple";
import { dialogFactory } from "./Dialog";

export class GridView {
  readonly #gridClassName: string;
  readonly #table: HTMLTableElement;
  readonly #pagination: WoltlabCorePaginationElement;
  readonly #baseUrl: string;
  readonly #filterButton: HTMLButtonElement;
  readonly #filterPills: HTMLElement;
  readonly #noItemsNotice: HTMLElement;
  #pageNo: number;
  #sortField: string;
  #sortOrder: string;
  #defaultSortField: string;
  #defaultSortOrder: string;
  #filters: Map<string, string>;
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
    this.#filterButton = document.getElementById(`${gridId}_filterButton`) as HTMLButtonElement;
    this.#filterPills = document.getElementById(`${gridId}_filters`) as HTMLElement;
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
    this.#initActions();
    this.#initFilters();
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
        this.#filters,
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

    this.#renderFilters(response.filterLabels);
    this.#initActions();
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
    if (this.#filters) {
      this.#filters.forEach((value, key) => {
        parameters.push([`filters[${key}]`, value]);
      });
    }

    if (parameters.length > 0) {
      url.search += url.search !== "" ? "&" : "?";
      url.search += new URLSearchParams(parameters).toString();
    }

    window.history.pushState({}, document.title, url.toString());
  }

  #initActions(): void {
    this.#table.querySelectorAll<HTMLTableRowElement>("tbody tr").forEach((row) => {
      row.querySelectorAll<HTMLElement>(".gridViewActions").forEach((element) => {
        let dropdown = UiDropdownSimple.getDropdownMenu(element.dataset.target!);
        if (!dropdown) {
          dropdown = element.closest(".dropdown")!.querySelector<HTMLElement>(".dropdownMenu")!;
        }

        dropdown?.querySelectorAll<HTMLButtonElement>("[data-action]").forEach((element) => {
          element.addEventListener("click", () => {
            row.dispatchEvent(
              new CustomEvent("action", {
                detail: element.dataset,
                bubbles: true,
              }),
            );
          });
        });
      });
    });
  }

  #initFilters(): void {
    if (!this.#filterButton) {
      return;
    }

    this.#filterButton.addEventListener(
      "click",
      promiseMutex(() => this.#showFilterDialog()),
    );

    if (!this.#filterPills) {
      return;
    }

    const filterButtons = this.#filterPills.querySelectorAll<HTMLButtonElement>("[data-filter]");
    if (!filterButtons.length) {
      return;
    }

    this.#filters = new Map<string, string>();
    filterButtons.forEach((button) => {
      this.#filters.set(button.dataset.filter!, button.dataset.filterValue!);
      button.addEventListener("click", () => {
        this.#removeFilter(button.dataset.filter!);
      });
    });
  }

  async #showFilterDialog(): Promise<void> {
    const url = new URL(this.#filterButton.dataset.endpoint!);
    if (this.#filters) {
      this.#filters.forEach((value, key) => {
        url.searchParams.set(`filters[${key}]`, value);
      });
    }

    const { ok, result } = await dialogFactory().usingFormBuilder().fromEndpoint(url.toString());

    if (ok) {
      this.#filters = new Map(Object.entries(result as ArrayLike<string>));
      this.#switchPage(1);
    }
  }

  #renderFilters(labels: ArrayLike<string>): void {
    if (!this.#filterPills) {
      return;
    }
    this.#filterPills.innerHTML = "";
    if (!this.#filters) {
      return;
    }

    this.#filters.forEach((value, key) => {
      const button = document.createElement("button");
      button.type = "button";
      button.classList.add("button", "small");
      const icon = document.createElement("fa-icon");
      icon.setIcon("circle-xmark");
      button.append(icon, labels[key]);
      button.addEventListener("click", () => {
        this.#removeFilter(key);
      });

      this.#filterPills.append(button);
    });
  }

  #removeFilter(filter: string): void {
    this.#filters.delete(filter);
    this.#switchPage(1);
  }

  #handlePopState(): void {
    let pageNo = 1;
    this.#sortField = this.#defaultSortField;
    this.#sortOrder = this.#defaultSortOrder;
    this.#filters = new Map<string, string>();

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
        this.#filters.set(matches[1], value);
      }
    });

    this.#switchPage(pageNo, false);
  }

  #initEventListeners(): void {
    this.#table.addEventListener("refresh", (event) => {
      void this.#refreshRow(event.target as HTMLElement);
    });
  }
}
