/**
 * Container visibility handler implementation for a wysiwyg tab menu that checks visibility
 * based on the visibility of its tab menu list items.
 *
 * @author  Olaf BRaun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since  6.2
 */
define(["require", "exports", "tslib", "./Abstract", "../Manager", "../../../../../Dom/Util"], function (require, exports, tslib_1, Abstract_1, DependencyManager, DomUtil) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.WysiwygTabMenu = void 0;
    Abstract_1 = tslib_1.__importDefault(Abstract_1);
    DependencyManager = tslib_1.__importStar(DependencyManager);
    DomUtil = tslib_1.__importStar(DomUtil);
    class WysiwygTabMenu extends Abstract_1.default {
        checkContainer() {
            // only consider containers that have not been hidden by their own dependencies
            if (DependencyManager.isHiddenByDependencies(this._container)) {
                return;
            }
            const containerIsVisible = !this._container.hidden;
            const listItems = this._container.parentNode.querySelectorAll("#" + DomUtil.identify(this._container) + " > nav > ul > li");
            const containerShouldBeVisible = Array.from(listItems).some((child) => !child.hidden);
            if (containerIsVisible !== containerShouldBeVisible) {
                this._container.hidden = !containerShouldBeVisible;
                // check containers again to make sure parent containers can react to
                // changing the visibility of this container
                DependencyManager.checkContainers();
            }
        }
    }
    exports.WysiwygTabMenu = WysiwygTabMenu;
});
