define(["require", "exports", "tslib", "../Api/Gridviews/GetRow", "../Api/Gridviews/GetRows", "../Dom/Change/Listener", "../Dom/Util", "../Helper/Selector", "../Ui/Dropdown/Simple", "./GridView/State"], function (require, exports, tslib_1, GetRow_1, GetRows_1, Listener_1, Util_1, Selector_1, Simple_1, State_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.GridView = void 0;
    Listener_1 = tslib_1.__importDefault(Listener_1);
    Util_1 = tslib_1.__importDefault(Util_1);
    Simple_1 = tslib_1.__importDefault(Simple_1);
    class GridView {
        #gridClassName;
        #table;
        #state;
        #noItemsNotice;
        #gridViewParameters;
        constructor(gridId, gridClassName, pageNo, baseUrl = "", sortField = "", sortOrder = "ASC", gridViewParameters) {
            this.#gridClassName = gridClassName;
            this.#table = document.getElementById(`${gridId}_table`);
            this.#noItemsNotice = document.getElementById(`${gridId}_noItemsNotice`);
            this.#gridViewParameters = gridViewParameters;
            this.#initInteractions();
            this.#state = this.#setupState(gridId, pageNo, baseUrl, sortField, sortOrder);
            this.#initEventListeners();
        }
        async #loadRows(source) {
            const response = (await (0, GetRows_1.getRows)(this.#gridClassName, this.#state.getPageNo(), this.#state.getSortField(), this.#state.getSortOrder(), this.#state.getActiveFilters(), this.#gridViewParameters)).unwrap();
            Util_1.default.setInnerHtml(this.#table.querySelector("tbody"), response.template);
            this.#table.hidden = response.totalRows == 0;
            this.#noItemsNotice.hidden = response.totalRows != 0;
            this.#state.updateFromResponse(source, response.pages, response.filterLabels);
            Listener_1.default.trigger();
        }
        async #refreshRow(row) {
            const response = (await (0, GetRow_1.getRow)(this.#gridClassName, row.dataset.objectId)).unwrap();
            row.replaceWith(Util_1.default.createFragmentFromHtml(response.template));
            Listener_1.default.trigger();
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
        #initEventListeners() {
            this.#table.addEventListener("refresh", (event) => {
                void this.#refreshRow(event.target);
            });
            this.#table.addEventListener("remove", (event) => {
                event.target.remove();
            });
        }
        #setupState(gridId, pageNo, baseUrl, sortField, sortOrder) {
            const state = new State_1.State(gridId, this.#table, pageNo, baseUrl, sortField, sortOrder);
            state.addEventListener("change", (event) => {
                void this.#loadRows(event.detail.source);
            });
            return state;
        }
    }
    exports.GridView = GridView;
});
