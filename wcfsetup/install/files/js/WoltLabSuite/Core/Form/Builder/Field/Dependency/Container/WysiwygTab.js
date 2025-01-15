/**
 * Container visibility handler implementation for a wysiwyg tab menu tab that, in addition to the
 * tab itself, also handles the visibility of the tab menu list item.
 *
 * @author  Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
define(["require", "exports", "tslib", "./Abstract", "../Manager", "WoltLabSuite/Core/Component/Message/MessageTabMenu"], function (require, exports, tslib_1, Abstract_1, DependencyManager, MessageTabMenu_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.WysiwygTab = void 0;
    Abstract_1 = tslib_1.__importDefault(Abstract_1);
    DependencyManager = tslib_1.__importStar(DependencyManager);
    class WysiwygTab extends Abstract_1.default {
        #tabName;
        #wysiwygId;
        constructor(containerId, tabName, wysiwygId) {
            super(containerId);
            this.#tabName = tabName;
            this.#wysiwygId = wysiwygId;
        }
        checkContainer() {
            // only consider containers that have not been hidden by their own dependencies
            if (DependencyManager.isHiddenByDependencies(this._container)) {
                return;
            }
            const containerIsVisible = !this._container.hidden;
            const tabMenu = (0, MessageTabMenu_1.getTabMenu)(this.#wysiwygId);
            const containerShouldBeVisible = tabMenu.isHiddenTab(this.#tabName);
            if (containerIsVisible !== containerShouldBeVisible) {
                if (containerShouldBeVisible) {
                    tabMenu?.showTab(this.#tabName);
                }
                else {
                    tabMenu?.hideTab(this.#tabName);
                }
                // Check containers again to make sure parent containers can react to changing the visibility
                // of this container.
                DependencyManager.checkContainers();
            }
        }
    }
    exports.WysiwygTab = WysiwygTab;
});
