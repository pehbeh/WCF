/**
 * Provides the program logic for the extended search form.
 *
 * @author  Marcel Werk
 * @copyright  2001-2021 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @woltlabExcludeBundle all
 */

import { dboAction } from "../../Ajax";
import DatePicker from "../../Date/Picker";
import * as DomUtil from "../../Dom/Util";
import { ucfirst } from "../../StringUtil";
import UiSearchInput from "./Input";
import * as UiScroll from "../Scroll";
import { setValues as setItemListValues } from "../ItemList";

type ResponseSearch = {
  count: number;
  title: string;
  pages?: number;
  pageNo?: number;
  searchID?: number;
  template?: string;
};

type ResponseSearchResults = {
  template: string;
};

type SearchParameters = string[][];

const enum SearchAction {
  Modify,
  Navigation,
  Init,
}

export class UiSearchExtended {
  private readonly form: HTMLFormElement;
  private readonly queryInput: HTMLInputElement;
  private readonly typeInput: HTMLSelectElement;
  private readonly delimiter: HTMLDivElement;
  private readonly filtersContainer: HTMLDetailsElement;
  private searchID: number | undefined = undefined;
  private pages = 0;
  private activePage = 1;
  private lastSearchRequest: AbortController | undefined = undefined;
  private lastSearchResultRequest: AbortController | undefined = undefined;
  private searchParameters: SearchParameters = [];

  constructor() {
    this.form = document.getElementById("extendedSearchForm") as HTMLFormElement;
    this.queryInput = document.getElementById("searchQuery") as HTMLInputElement;
    this.typeInput = document.getElementById("searchType") as HTMLSelectElement;
    this.filtersContainer = document.querySelector(".searchFiltersContainer") as HTMLDetailsElement;
    this.delimiter = document.createElement("div");

    this.form.insertAdjacentElement("afterend", this.delimiter);
    this.initEventListener();
    this.initKeywordSuggestions();
    this.initQueryString();
  }

  private initEventListener(): void {
    this.form.addEventListener("submit", (event) => {
      event.preventDefault();
      this.activePage = 1;
      void this.search(SearchAction.Modify);
    });
    this.typeInput.addEventListener("change", () => this.changeType());

    window.addEventListener("popstate", () => {
      this.initQueryString();
    });
  }

  private initKeywordSuggestions(): void {
    new UiSearchInput(this.queryInput, {
      ajax: {
        className: "wcf\\data\\search\\keyword\\SearchKeywordAction",
      },
      autoFocus: false,
    });
  }

  private changeType(): void {
    let hasVisibleFilters = false;
    document.querySelectorAll(".objectTypeSearchFilters").forEach((filter: HTMLElement) => {
      if (filter.dataset.objectType === this.typeInput.value) {
        hasVisibleFilters = true;
        filter.hidden = false;
      } else {
        filter.hidden = true;
      }
    });

    const title = document.querySelector(".searchFiltersTitle") as HTMLElement;
    if (hasVisibleFilters) {
      const selectedOption = this.typeInput.selectedOptions.item(0)!;
      title.textContent = selectedOption.textContent!.trim();

      title.hidden = false;
    } else {
      title.hidden = true;
    }
  }

  private async search(searchAction: SearchAction): Promise<void> {
    if (!this.queryInput.value.trim()) {
      return;
    }

    this.updateQueryString(searchAction);

    this.lastSearchRequest?.abort();

    const request = dboAction("search", "wcf\\data\\search\\SearchAction").payload(this.getFormData());
    this.lastSearchRequest = request.getAbortController();
    const { count, searchID, title, pages, pageNo, template } = (await request.dispatch()) as ResponseSearch;

    document.querySelector(".contentTitle")!.textContent = title;
    this.searchID = searchID;

    this.removeSearchResults();

    if (count > 0) {
      this.pages = pages!;
      this.activePage = pageNo!;
      this.showSearchResults(template!);
    } else if (Object.keys(this.getFormData()).length > 4) {
      // Show the advanced filters when there are no results
      // but advanced filters are applied.
      this.filtersContainer.open = true;
    }
  }

  private updateQueryString(searchAction: SearchAction): void {
    const url = new URL(this.form.action);
    url.search += url.search !== "" ? "&" : "?";

    if (searchAction !== SearchAction.Navigation) {
      this.searchParameters = [];
      new FormData(this.form).forEach((value, key) => {
        // eslint-disable-next-line @typescript-eslint/no-base-to-string
        const trimmed = value.toString().trim();
        if (trimmed) {
          this.searchParameters.push([key, trimmed]);
        }
      });
    }
    const parameters = this.searchParameters.slice();

    if (this.activePage > 1) {
      parameters.push(["pageNo", this.activePage.toString()]);
    }
    url.search += new URLSearchParams(parameters).toString();

    if (searchAction === SearchAction.Init) {
      window.history.replaceState({ searchAction }, document.title, url.toString());
    } else {
      window.history.pushState({ searchAction }, document.title, url.toString());
    }
  }

  private getFormData(): Record<string, unknown> {
    const data = {};
    new FormData(this.form).forEach((value, key) => {
      // eslint-disable-next-line @typescript-eslint/no-base-to-string
      if (value.toString()) {
        data[key] = value;
      }
    });
    if (this.activePage > 1) {
      data["pageNo"] = this.activePage;
    }

    return data;
  }

  private initQueryString(): void {
    this.activePage = 1;

    const url = new URL(window.location.href);
    url.searchParams.forEach((value, key) => {
      if (key === "pageNo") {
        this.activePage = parseInt(value, 10);
        if (this.activePage < 1) this.activePage = 1;
        return;
      }

      const element = this.form.elements[key] as HTMLElement;
      if (value && element) {
        if (element instanceof RadioNodeList) {
          let id = "";
          element.forEach((childElement: HTMLElement) => {
            if (childElement.classList.contains("inputDatePicker")) {
              id = childElement.id;
            }
          });
          if (id) {
            DatePicker.setDate(id, new Date(value));
            return;
          }

          element.value = value;
        } else if (element instanceof HTMLInputElement) {
          if (element.classList.contains("itemListInputShadow")) {
            const itemList = element.nextElementSibling as HTMLElement;
            if (itemList?.classList.contains("inputItemList")) {
              setItemListValues(
                itemList.dataset.elementId!,
                value.split(",").map((value) => {
                  return {
                    objectId: 0,
                    value: value.trim(),
                  };
                }),
              );
            }

            return;
          }

          if (element.type === "checkbox") {
            element.checked = true;
          } else {
            element.value = value;
          }
        } else if (element instanceof HTMLSelectElement) {
          element.value = value;
        }
      }
    });

    this.typeInput.dispatchEvent(new Event("change"));
    void this.search(SearchAction.Init);
  }

  private initPagination(position: "top" | "bottom"): void {
    const wrapperDiv = document.createElement("div");
    wrapperDiv.classList.add("pagination" + ucfirst(position));
    this.form.parentElement!.insertBefore(wrapperDiv, this.delimiter);

    const pagination = document.createElement("woltlab-core-pagination");
    pagination.page = this.activePage;
    pagination.count = this.pages;
    pagination.behavior = "button";
    pagination.url = this.getPaginationUrl();
    pagination.addEventListener("switchPage", (event: CustomEvent) => {
      void this.changePage(event.detail).then(() => {
        if (position === "bottom") {
          UiScroll.element(this.form.nextElementSibling as HTMLElement, undefined, "auto");
        }
      });
    });

    wrapperDiv.append(pagination);
  }

  private getPaginationUrl(): string {
    const url = new URL(this.form.action);
    url.search += url.search !== "" ? "&" : "?";

    const searchParameters: SearchParameters = [];
    new FormData(this.form).forEach((value, key) => {
      // eslint-disable-next-line @typescript-eslint/no-base-to-string
      const trimmed = value.toString().trim();
      if (trimmed) {
        searchParameters.push([key, trimmed]);
      }
    });
    const parameters = searchParameters.slice();

    if (this.activePage > 1) {
      parameters.push(["pageNo", this.activePage.toString()]);
    }
    url.search += new URLSearchParams(parameters).toString();

    return url.toString();
  }

  private async changePage(pageNo: number): Promise<void> {
    this.lastSearchResultRequest?.abort();

    const request = dboAction("getSearchResults", "wcf\\data\\search\\SearchAction").payload({
      searchID: this.searchID,
      pageNo,
    });
    this.lastSearchResultRequest = request.getAbortController();
    const { template } = (await request.dispatch()) as ResponseSearchResults;
    this.activePage = pageNo;
    this.removeSearchResults();
    this.showSearchResults(template);
    this.updateQueryString(SearchAction.Navigation);
  }

  private removeSearchResults(): void {
    while (this.form.nextSibling !== null && this.form.nextSibling !== this.delimiter) {
      this.form.parentElement!.removeChild(this.form.nextSibling);
    }
  }

  private showSearchResults(template: string): void {
    if (this.pages > 1) {
      this.initPagination("top");
    }

    const fragment = DomUtil.createFragmentFromHtml(template);
    this.form.parentElement!.insertBefore(fragment, this.delimiter);

    if (this.pages > 1) {
      this.initPagination("bottom");
    }
  }
}

export default UiSearchExtended;
