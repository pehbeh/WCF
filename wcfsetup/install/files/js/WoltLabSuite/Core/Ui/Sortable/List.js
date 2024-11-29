/**
 * Sortable lists with optimized handling per device sizes.
 *
 * @author  Olaf Braun, Alexander Ebert
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @woltlabExcludeBundle tiny
 */
define(["require", "exports", "tslib", "../../Core", "sortablejs", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Ui/Notification", "WoltLabSuite/Core/Language"], function (require, exports, tslib_1, Core, sortablejs_1, Ajax_1, Notification_1, Language_1) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    sortablejs_1 = tslib_1.__importDefault(sortablejs_1);
    function getNestingLevel(element, container) {
        let nestingLevel = 0;
        let sortableNode = sortablejs_1.default.utils.closest(element, ".sortableNode");
        while (sortableNode !== null &&
            (!container || (sortableNode !== container && sortableNode.parentNode !== container))) {
            sortableNode = sortablejs_1.default.utils.closest(sortableNode.parentElement, ".sortableNode");
            nestingLevel++;
        }
        return nestingLevel;
    }
    class UiSortableList {
        _options;
        #container;
        #sortables = new Map();
        /**
         * Initializes the sortable list controller.
         */
        constructor(opts) {
            this._options = Core.extend({
                containerId: "",
                className: "",
                offset: 0,
                maxNestingLevel: undefined,
                toleranceElement: "span",
                options: {
                    animation: 150,
                    fallbackOnBody: true,
                    dataIdAttr: "data-object-id",
                    chosenClass: "sortablePlaceholder",
                    ghostClass: "",
                    draggable: "li",
                    filter: this.#filter.bind(this),
                    onMove: this.#onMove.bind(this),
                    onEnd: this.#onEnd.bind(this),
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
                    this._options.options.draggable = "tr";
                    this._options.toleranceElement = undefined;
                }
                this.#sortables.set(sortableList.dataset.objectId ? parseInt(sortableList.dataset.objectId, 10) : 0, new sortablejs_1.default(sortableList, {
                    direction: "vertical",
                    ...this._options.options,
                }));
            }
            else {
                this.#container.querySelectorAll(".sortableList").forEach((list) => {
                    this.#sortables.set(parseInt(list.dataset.objectId, 10), new sortablejs_1.default(list, {
                        group: "nested",
                        ...this._options.options,
                    }));
                });
            }
            if (this._options.className) {
                let formSubmit = this.#container.querySelector(".formSubmit");
                if (!formSubmit) {
                    formSubmit = this.#container.nextElementSibling;
                }
                if (!formSubmit) {
                    console.debug("[UiSortableList] Unable to find form submit for saving, aborting.");
                    return;
                }
                formSubmit.querySelector('button[data-type="submit"]')?.addEventListener("click", () => {
                    void this.save();
                });
            }
        }
        async save() {
            if (!this._options.className) {
                return;
            }
            const structure = Object.fromEntries(Array.from(this.#sortables).map(([objectId, sortable]) => [objectId, sortable.toArray()]));
            const parameters = Core.extend({
                data: {
                    offset: this._options.offset,
                    structure: structure,
                },
            }, this._options.additionalParameters);
            await (0, Ajax_1.dboAction)("updatePosition", this._options.className).payload(parameters).dispatch();
            (0, Notification_1.show)((0, Language_1.getPhrase)("wcf.global.success.edit"));
        }
        #onMove(event) {
            if (this._options.maxNestingLevel === undefined) {
                return true;
            }
            const closest = sortablejs_1.default.utils.closest(event.to, ".sortableNode");
            if (!closest) {
                // Top level
                return true;
            }
            if (closest && sortablejs_1.default.utils.is(closest, ".sortableNoNesting")) {
                return false;
            }
            const levelOfDraggedNode = Math.max(...Array.from(event.dragged.querySelectorAll(".sortableList")).map((list) => {
                return getNestingLevel(list, event.dragged);
            }));
            if (getNestingLevel(event.to) + levelOfDraggedNode > this._options.maxNestingLevel) {
                return false;
            }
            return true;
        }
        #onEnd(event) {
            if (this._options.maxNestingLevel === undefined) {
                return;
            }
            event.to.querySelectorAll(".sortableNode").forEach((node) => {
                if (getNestingLevel(node) > this._options.maxNestingLevel) {
                    node.classList.add("sortableNoNesting");
                }
                else {
                    node.classList.remove("sortableNoNesting");
                }
            });
        }
        #filter(event, target) {
            if (sortablejs_1.default.utils.is(target, ".sortableNoSorting")) {
                return true;
            }
            const eventTarget = event.target;
            if (eventTarget === target) {
                return false;
            }
            if (eventTarget.parentElement === target) {
                return false;
            }
            if (!this._options.toleranceElement) {
                return true;
            }
            return !sortablejs_1.default.utils.is(eventTarget, this._options.toleranceElement);
        }
    }
    return UiSortableList;
});
