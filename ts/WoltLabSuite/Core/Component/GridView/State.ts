import Filter from "./Filter";
import Sorting from "./Sorting";

export const enum UpdateCause {
  Change,
  History,
  Pagination,
}

// eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
export class State extends EventTarget {
  readonly #baseUrl: string;
  readonly #filter: Filter;
  readonly #pagination: WoltlabCorePaginationElement;
  readonly #sorting: Sorting;
  #pageNo: number;

  constructor(
    gridId: string,
    table: HTMLTableElement,
    pageNo: number,
    baseUrl: string,
    sortField: string,
    sortOrder: string,
  ) {
    super();

    this.#baseUrl = baseUrl;
    this.#pageNo = pageNo;

    this.#pagination = document.getElementById(`${gridId}_pagination`) as WoltlabCorePaginationElement;
    this.#pagination.addEventListener("switchPage", (event: CustomEvent) => {
      void this.#switchPage(event.detail, UpdateCause.Pagination);
    });

    this.#filter = new Filter(gridId);
    this.#filter.addEventListener("change", () => {
      this.#switchPage(1, UpdateCause.Change);
    });

    this.#sorting = new Sorting(table, sortField, sortOrder);
    this.#sorting.addEventListener("change", () => {
      this.#switchPage(1, UpdateCause.Change);
    });

    window.addEventListener("popstate", () => {
      this.#handlePopState();
    });
  }

  getPageNo(): number {
    return this.#pageNo;
  }

  getSortField(): string {
    return this.#sorting.getSortField();
  }

  getSortOrder(): string {
    return this.#sorting.getSortOrder();
  }

  getActiveFilters(): Map<string, string> {
    return this.#filter.getActiveFilters();
  }

  updateFromResponse(source: UpdateCause, count: number, filterLabels: ArrayLike<string>): void {
    this.#filter.setFilterLabels(filterLabels);
    this.#pagination.count = count;

    if (source === UpdateCause.Change || source === UpdateCause.Pagination) {
      this.#updateQueryString();
    }
  }

  #switchPage(pageNo: number, source: UpdateCause): void {
    this.#pagination.page = pageNo;
    this.#pageNo = pageNo;

    this.dispatchEvent(new CustomEvent("change", { detail: { source } }));
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

    for (const parameter of this.#sorting.getQueryParameters()) {
      parameters.push(parameter);
    }

    for (const parameter of this.#filter.getQueryParameters()) {
      parameters.push(parameter);
    }

    if (parameters.length > 0) {
      url.search += url.search !== "" ? "&" : "?";
      url.search += new URLSearchParams(parameters).toString();
    }

    window.history.pushState({}, document.title, url.toString());
  }

  #handlePopState(): void {
    let pageNo = 1;

    const { searchParams } = new URL(window.location.href);
    const value = searchParams.get("pageNo");
    if (value !== null) {
      pageNo = parseInt(value);
      if (Number.isNaN(pageNo) || pageNo < 1) {
        pageNo = 1;
      }
    }

    this.#filter.updateFromSearchParams(searchParams);
    this.#sorting.updateFromSearchParams(searchParams);

    this.#switchPage(pageNo, UpdateCause.History);
  }
}

interface StateEventMap {
  change: CustomEvent<{ source: UpdateCause }>;
}

// eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
export interface State extends EventTarget {
  addEventListener: {
    <T extends keyof StateEventMap>(
      type: T,
      listener: (this: State, ev: StateEventMap[T]) => any,
      options?: boolean | AddEventListenerOptions,
    ): void;
  } & HTMLElement["addEventListener"];
}

export default State;
