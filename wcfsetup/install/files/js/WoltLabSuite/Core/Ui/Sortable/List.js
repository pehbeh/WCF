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
                    chosenClass: "sortablePlaceholder",
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    filter: (event, target, sortable) => {
                        console.log(event, target, sortable);
                        return true;
                    },
                },
                isSimpleSorting: false,
                additionalParameters: {},
            }, opts);
            this.#container = document.getElementById(this._options.containerId);
            if (!this.#container) {
                throw new Error(`Container '${this._options.containerId}' not found.`);
            }
            new sortablejs_1.default(this.#container.querySelector(".sortableList"), this._options.options);
        }
    }
    return UiSortableList;
});
