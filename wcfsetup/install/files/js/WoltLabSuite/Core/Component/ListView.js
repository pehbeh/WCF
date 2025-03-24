define(["require", "exports", "tslib", "./ListView/State", "../Dom/Change/Listener", "../Dom/Util", "../Api/ListViews/GetItems", "WoltLabSuite/Core/Ui/Scroll", "../Helper/Selector", "../Ui/Dropdown/Simple"], function (require, exports, tslib_1, State_1, Listener_1, Util_1, GetItems_1, Scroll_1, Selector_1, Simple_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.ListView = void 0;
    State_1 = tslib_1.__importDefault(State_1);
    Simple_1 = tslib_1.__importDefault(Simple_1);
    class ListView {
        #viewClassName;
        #viewElement;
        #state;
        #noItemsNotice;
        constructor(viewId, viewClassName, pageNo, baseUrl = "", sortField = "", sortOrder = "ASC") {
            this.#viewClassName = viewClassName;
            this.#viewElement = document.getElementById(`${viewId}_items`);
            this.#noItemsNotice = document.getElementById(`${viewId}_noItemsNotice`);
            this.#initInteractions();
            this.#state = this.#setupState(viewId, pageNo, baseUrl, sortField, sortOrder);
        }
        #setupState(viewId, pageNo, baseUrl, sortField, sortOrder) {
            const state = new State_1.default(viewId, this.#viewElement, pageNo, baseUrl, sortField, sortOrder);
            state.addEventListener("list-view:change", (event) => {
                void this.#loadItems(event.detail.source);
            });
            /*state.addEventListener("grid-view:get-bulk-interactions", (event) => {
              void this.#loadBulkInteractions(event.detail.objectIds);
            });*/
            return state;
        }
        async #loadItems(cause) {
            const response = (await (0, GetItems_1.getItems)(this.#viewClassName, this.#state.getPageNo(), this.#state.getSortField(), this.#state.getSortOrder(), this.#state.getActiveFilters())).unwrap();
            (0, Util_1.setInnerHtml)(this.#viewElement, response.template);
            this.#viewElement.hidden = response.totalItems === 0;
            this.#noItemsNotice.hidden = response.totalItems !== 0;
            this.#state.updateFromResponse(cause, response.pages, response.filterLabels);
            if (cause === 2 /* StateChangeCause.Pagination */) {
                (0, Scroll_1.element)(this.#viewElement);
            }
            (0, Listener_1.trigger)();
        }
        #initInteractions() {
            (0, Selector_1.wheneverFirstSeen)(`#${this.#viewElement.id} .listView__item`, (item) => {
                item.querySelectorAll(".dropdownToggle").forEach((element) => {
                    let dropdown = Simple_1.default.getDropdownMenu(element.dataset.target);
                    if (!dropdown) {
                        dropdown = element.closest(".dropdown").querySelector(".dropdownMenu");
                    }
                    dropdown?.querySelectorAll("[data-interaction]").forEach((element) => {
                        element.addEventListener("click", () => {
                            item.dispatchEvent(new CustomEvent("interaction:execute", {
                                detail: element.dataset,
                                bubbles: true,
                            }));
                        });
                    });
                });
            });
        }
    }
    exports.ListView = ListView;
});
