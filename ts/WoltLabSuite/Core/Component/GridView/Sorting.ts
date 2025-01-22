// eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
export class Sorting extends EventTarget {
  #defaultSortField: string;
  #defaultSortOrder: string;
  #sortField: string;
  #sortOrder: string;
  #table: HTMLTableElement;

  constructor(table: HTMLTableElement, sortField: string, sortOrder: string) {
    super();

    this.#sortField = sortField;
    this.#defaultSortField = sortField;
    this.#sortOrder = sortOrder;
    this.#defaultSortOrder = sortOrder;
    this.#table = table;

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

  getSortField(): string {
    return this.#sortField;
  }

  getSortOrder(): string {
    return this.#sortOrder;
  }

  resetSorting(): void {
    this.#sortField = this.#defaultSortField;
    this.#sortOrder = this.#defaultSortOrder;
  }

  setSortField(sortField: string): void {
    this.#sortField = sortField;
  }

  setSortOrder(sortOrder: string): void {
    this.#sortOrder = sortOrder;
  }

  #sort(sortField: string): void {
    if (this.#sortField == sortField && this.#sortOrder == "ASC") {
      this.#sortOrder = "DESC";
    } else {
      this.#sortField = sortField;
      this.#sortOrder = "ASC";
    }

    this.dispatchEvent(new CustomEvent("switchPage", { detail: { pageNo: 1 } }));
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
}

interface SortingEventMap {
  switchPage: CustomEvent<{ pageNo: number }>;
}

// eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
export interface Sorting extends EventTarget {
  addEventListener: {
    <T extends keyof SortingEventMap>(
      type: T,
      listener: (this: Sorting, ev: SortingEventMap[T]) => any,
      options?: boolean | AddEventListenerOptions,
    ): void;
  } & HTMLElement["addEventListener"];
}

export default Sorting;
