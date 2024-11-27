/**
 * Sortable lists with optimized handling per device sizes.
 *
 * @author  Alexander Ebert
 * @copyright  2001-2019 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @woltlabExcludeBundle tiny
 */
define(["require", "exports", "tslib", "../../Core", "sortablejs"], function (require, exports, tslib_1, Core, sortablejs_1) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    sortablejs_1 = tslib_1.__importDefault(sortablejs_1);
    class UiSortableList {
        _options;
        #container;
        /**
         * Initializes the sortable list controller.
         */
        constructor(opts) {
            this._options = Core.extend({
                containerId: "",
                className: "",
                offset: 0,
                options: {
                    animation: 150,
                    swapThreshold: 0.65,
                    fallbackOnBody: true,
                    chosenClass: "sortablePlaceholder",
                    ghostClass: "",
                    draggable: "li:not(.sortableNoSorting)",
                    toleranceElement: "span",
                    filter: (event, target) => {
                        const eventTarget = event.target;
                        if (eventTarget === target) {
                            return false;
                        }
                        if (eventTarget.parentElement !== target) {
                            return false;
                        }
                        return eventTarget.nodeName !== this._options.toleranceElement;
                    },
                },
                isSimpleSorting: false,
                additionalParameters: {},
            }, opts);
            this.#container = document.getElementById(this._options.containerId);
            if (!this.#container) {
                throw new Error(`Container '${this._options.containerId}' not found.`);
            }
            if (this._options.isSimpleSorting) {
                const sortableList = this.#container.querySelector(".sortableList");
                if (sortableList.nodeName === "TBODY") {
                    this._options.options.draggable = "tr:not(.sortableNoSorting)";
                }
                new sortablejs_1.default(sortableList, {
                    direction: "vertical",
                    ...this._options.options,
                });
            }
            else {
                this.#container.querySelectorAll(".sortableList").forEach((list) => {
                    new sortablejs_1.default(list, {
                        group: "nested",
                        ...this._options.options,
                    });
                });
            }
        }
    }
    return UiSortableList;
});
