define(["require", "exports", "tslib", "../Api/Gridviews/GetRow", "../Api/Gridviews/GetRows", "../Dom/Change/Listener", "../Dom/Util", "../Helper/PromiseMutex", "../Ui/Dropdown/Simple", "./Dialog"], function (require, exports, tslib_1, GetRow_1, GetRows_1, Listener_1, Util_1, PromiseMutex_1, Simple_1, Dialog_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.GridView = void 0;
    Listener_1 = tslib_1.__importDefault(Listener_1);
    Util_1 = tslib_1.__importDefault(Util_1);
    Simple_1 = tslib_1.__importDefault(Simple_1);
    class GridView {
        #gridClassName;
        #table;
        #pagination;
        #baseUrl;
        #filterButton;
        #filterPills;
        #noItemsNotice;
        #pageNo;
        #sortField;
        #sortOrder;
        #defaultSortField;
        #defaultSortOrder;
        #filters;
        #gridViewParameters;
        constructor(gridId, gridClassName, pageNo, baseUrl = "", sortField = "", sortOrder = "ASC", gridViewParameters) {
            this.#gridClassName = gridClassName;
            this.#table = document.getElementById(`${gridId}_table`);
            this.#pagination = document.getElementById(`${gridId}_pagination`);
            this.#filterButton = document.getElementById(`${gridId}_filterButton`);
            this.#filterPills = document.getElementById(`${gridId}_filters`);
            this.#noItemsNotice = document.getElementById(`${gridId}_noItemsNotice`);
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
        #initPagination() {
            this.#pagination.addEventListener("switchPage", (event) => {
                void this.#switchPage(event.detail);
            });
        }
        #initSorting() {
            this.#table
                .querySelectorAll('.gridView__headerColumn[data-sortable="1"]')
                .forEach((element) => {
                const button = element.querySelector(".gridView__headerColumn__button");
                button?.addEventListener("click", () => {
                    this.#sort(element.dataset.id);
                });
            });
            this.#renderActiveSorting();
        }
        #sort(sortField) {
            if (this.#sortField == sortField && this.#sortOrder == "ASC") {
                this.#sortOrder = "DESC";
            }
            else {
                this.#sortField = sortField;
                this.#sortOrder = "ASC";
            }
            this.#switchPage(1);
            this.#renderActiveSorting();
        }
        #renderActiveSorting() {
            this.#table.querySelectorAll('th[data-sortable="1"]').forEach((element) => {
                element.classList.remove("active", "ASC", "DESC");
                if (element.dataset.id == this.#sortField) {
                    element.classList.add("active", this.#sortOrder);
                }
            });
        }
        #switchPage(pageNo, updateQueryString = true) {
            this.#pagination.page = pageNo;
            this.#pageNo = pageNo;
            void this.#loadRows(updateQueryString);
        }
        async #loadRows(updateQueryString = true) {
            const response = (await (0, GetRows_1.getRows)(this.#gridClassName, this.#pageNo, this.#sortField, this.#sortOrder, this.#filters, this.#gridViewParameters)).unwrap();
            Util_1.default.setInnerHtml(this.#table.querySelector("tbody"), response.template);
            this.#table.hidden = response.totalRows == 0;
            this.#noItemsNotice.hidden = response.totalRows != 0;
            this.#pagination.count = response.pages;
            if (updateQueryString) {
                this.#updateQueryString();
            }
            Listener_1.default.trigger();
            this.#renderFilters(response.filterLabels);
            this.#initActions();
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
        #initActions() {
            this.#table.querySelectorAll("tbody tr").forEach((row) => {
                row.querySelectorAll(".gridViewActions").forEach((element) => {
                    let dropdown = Simple_1.default.getDropdownMenu(element.dataset.target);
                    if (!dropdown) {
                        dropdown = element.closest(".dropdown").querySelector(".dropdownMenu");
                    }
                    dropdown?.querySelectorAll("[data-action]").forEach((element) => {
                        element.addEventListener("click", () => {
                            row.dispatchEvent(new CustomEvent("action", {
                                detail: element.dataset,
                                bubbles: true,
                            }));
                        });
                    });
                });
            });
        }
        #initFilters() {
            if (!this.#filterButton) {
                return;
            }
            this.#filterButton.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => this.#showFilterDialog()));
            if (!this.#filterPills) {
                return;
            }
            const filterButtons = this.#filterPills.querySelectorAll("[data-filter]");
            if (!filterButtons.length) {
                return;
            }
            this.#filters = new Map();
            filterButtons.forEach((button) => {
                this.#filters.set(button.dataset.filter, button.dataset.filterValue);
                button.addEventListener("click", () => {
                    this.#removeFilter(button.dataset.filter);
                });
            });
        }
        async #showFilterDialog() {
            const url = new URL(this.#filterButton.dataset.endpoint);
            if (this.#filters) {
                this.#filters.forEach((value, key) => {
                    url.searchParams.set(`filters[${key}]`, value);
                });
            }
            const { ok, result } = await (0, Dialog_1.dialogFactory)().usingFormBuilder().fromEndpoint(url.toString());
            if (ok) {
                this.#filters = new Map(Object.entries(result));
                this.#switchPage(1);
            }
        }
        #renderFilters(labels) {
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
        #removeFilter(filter) {
            this.#filters.delete(filter);
            this.#switchPage(1);
        }
        #handlePopState() {
            let pageNo = 1;
            this.#sortField = this.#defaultSortField;
            this.#sortOrder = this.#defaultSortOrder;
            this.#filters = new Map();
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
        #initEventListeners() {
            this.#table.addEventListener("refresh", (event) => {
                void this.#refreshRow(event.target);
            });
        }
    }
    exports.GridView = GridView;
});
