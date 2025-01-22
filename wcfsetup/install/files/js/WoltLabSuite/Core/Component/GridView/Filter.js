define(["require", "exports", "../../Helper/PromiseMutex", "../Dialog"], function (require, exports, PromiseMutex_1, Dialog_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.Filter = void 0;
    // eslint-disable-next-line @typescript-eslint/no-unsafe-declaration-merging
    class Filter extends EventTarget {
        #filterButton;
        #filterPills;
        #filters = new Map();
        constructor(gridId) {
            super();
            this.#filterButton = document.getElementById(`${gridId}_filterButton`);
            this.#filterPills = document.getElementById(`${gridId}_filters`);
            this.#setupEventListeners();
        }
        resetFilters() {
            this.#filters.clear();
        }
        setFilter(key, value) {
            this.#filters.set(key, value);
        }
        getActiveFilters() {
            return new Map(this.#filters);
        }
        setFilterLabels(labels) {
            if (this.#filterPills === null) {
                return;
            }
            this.#filterPills.innerHTML = "";
            if (this.#filters.size === 0) {
                return;
            }
            for (const key of this.#filters.keys()) {
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
            }
        }
        #setupEventListeners() {
            if (this.#filterButton === null) {
                return;
            }
            this.#filterButton.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => this.#showFilterDialog()));
            if (this.#filterPills === null) {
                return;
            }
            const filterButtons = this.#filterPills.querySelectorAll("[data-filter]");
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
                this.dispatchEvent(new CustomEvent("switchPage", { detail: { pageNo: 1 } }));
            }
        }
        #removeFilter(filter) {
            this.#filters.delete(filter);
            this.dispatchEvent(new CustomEvent("switchPage", { detail: { pageNo: 1 } }));
        }
    }
    exports.Filter = Filter;
    exports.default = Filter;
});
