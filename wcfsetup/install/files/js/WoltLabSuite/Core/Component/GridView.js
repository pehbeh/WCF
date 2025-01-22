define(["require", "exports", "tslib", "../Api/Gridviews/GetRow", "../Api/Gridviews/GetRows", "../Dom/Change/Listener", "../Dom/Util", "../Helper/Selector", "../Ui/Dropdown/Simple", "./GridView/Filter", "./GridView/Sorting"], function (require, exports, tslib_1, GetRow_1, GetRows_1, Listener_1, Util_1, Selector_1, Simple_1, Filter_1, Sorting_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.GridView = void 0;
    Listener_1 = tslib_1.__importDefault(Listener_1);
    Util_1 = tslib_1.__importDefault(Util_1);
    Simple_1 = tslib_1.__importDefault(Simple_1);
    Filter_1 = tslib_1.__importDefault(Filter_1);
    Sorting_1 = tslib_1.__importDefault(Sorting_1);
    class GridView {
        #filter;
        #gridClassName;
        #table;
        #pagination;
        #sorting;
        #baseUrl;
        #noItemsNotice;
        #pageNo;
        #gridViewParameters;
        constructor(gridId, gridClassName, pageNo, baseUrl = "", sortField = "", sortOrder = "ASC", gridViewParameters) {
            this.#gridClassName = gridClassName;
            this.#table = document.getElementById(`${gridId}_table`);
            this.#pagination = document.getElementById(`${gridId}_pagination`);
            this.#noItemsNotice = document.getElementById(`${gridId}_noItemsNotice`);
            this.#pageNo = pageNo;
            this.#baseUrl = baseUrl;
            this.#gridViewParameters = gridViewParameters;
            this.#initPagination();
            this.#initSorting();
            this.#initInteractions();
            this.#filter = this.#setupFilter(gridId);
            this.#sorting = this.#setupSorting(sortField, sortOrder);
            this.#initEventListeners();
            window.addEventListener("popstate", () => {
                this.#handlePopState();
            });
        }
        #initPagination() {
            this.#pagination.addEventListener("switchPage", (event) => {
                void this.#switchPage(event.detail);
            });
        }
        #initSorting() { }
        #switchPage(pageNo, updateQueryString = true) {
            this.#pagination.page = pageNo;
            this.#pageNo = pageNo;
            void this.#loadRows(updateQueryString);
        }
        async #loadRows(updateQueryString = true) {
            const response = (await (0, GetRows_1.getRows)(this.#gridClassName, this.#pageNo, this.#sorting.getSortField(), this.#sorting.getSortOrder(), this.#filter.getActiveFilters(), this.#gridViewParameters)).unwrap();
            Util_1.default.setInnerHtml(this.#table.querySelector("tbody"), response.template);
            this.#table.hidden = response.totalRows == 0;
            this.#noItemsNotice.hidden = response.totalRows != 0;
            this.#pagination.count = response.pages;
            if (updateQueryString) {
                this.#updateQueryString();
            }
            Listener_1.default.trigger();
            this.#filter.setFilterLabels(response.filterLabels);
        }
        async #refreshRow(row) {
            const response = (await (0, GetRow_1.getRow)(this.#gridClassName, row.dataset.objectId)).unwrap();
            row.replaceWith(Util_1.default.createFragmentFromHtml(response.template));
            Listener_1.default.trigger();
        }
        #updateQueryString() {
            if (!this.#baseUrl) {
                return;
            }
            const url = new URL(this.#baseUrl);
            const parameters = [];
            if (this.#pageNo > 1) {
                parameters.push(["pageNo", this.#pageNo.toString()]);
            }
            if (this.#sorting.getSortField()) {
                parameters.push(["sortField", this.#sorting.getSortField()]);
                parameters.push(["sortOrder", this.#sorting.getSortOrder()]);
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
        #initInteractions() {
            (0, Selector_1.wheneverFirstSeen)(`#${this.#table.id} tbody tr`, (row) => {
                row.querySelectorAll(".dropdownToggle").forEach((element) => {
                    let dropdown = Simple_1.default.getDropdownMenu(element.dataset.target);
                    if (!dropdown) {
                        dropdown = element.closest(".dropdown").querySelector(".dropdownMenu");
                    }
                    dropdown?.querySelectorAll("[data-interaction]").forEach((element) => {
                        element.addEventListener("click", () => {
                            row.dispatchEvent(new CustomEvent("interaction", {
                                detail: element.dataset,
                                bubbles: true,
                            }));
                        });
                    });
                });
            });
        }
        #handlePopState() {
            let pageNo = 1;
            this.#sorting.resetSorting();
            this.#filter.resetFilters();
            const url = new URL(window.location.href);
            url.searchParams.forEach((value, key) => {
                if (key === "pageNo") {
                    pageNo = parseInt(value, 10);
                    return;
                }
                if (key === "sortField") {
                    this.#sorting.setSortField(value);
                }
                if (key === "sortOrder") {
                    this.#sorting.setSortOrder(value);
                }
                const matches = key.match(/^filters\[([a-z0-9_]+)\]$/i);
                if (matches) {
                    this.#filter.setFilter(matches[1], value);
                }
            });
            this.#switchPage(pageNo, false);
        }
        #initEventListeners() {
            this.#table.addEventListener("refresh", (event) => {
                void this.#refreshRow(event.target);
            });
            this.#table.addEventListener("remove", (event) => {
                event.target.remove();
            });
        }
        #setupFilter(gridId) {
            const filter = new Filter_1.default(gridId);
            filter.addEventListener("switchPage", (event) => {
                this.#switchPage(event.detail.pageNo);
            });
            return filter;
        }
        #setupSorting(sortField, sortOrder) {
            const sorting = new Sorting_1.default(this.#table, sortField, sortOrder);
            sorting.addEventListener("switchPage", (event) => {
                this.#switchPage(event.detail.pageNo);
            });
            return sorting;
        }
    }
    exports.GridView = GridView;
});
