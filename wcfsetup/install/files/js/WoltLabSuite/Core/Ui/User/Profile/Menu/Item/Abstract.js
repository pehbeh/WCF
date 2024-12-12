/**
 * Default implementation for user interaction menu items used in the user profile.
 *
 * @author  Alexander Ebert
 * @copyright  2001-2019 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @woltlabExcludeBundle all
 */
define(["require", "exports", "tslib", "../../../../../Ajax"], function (require, exports, tslib_1, Ajax) {
    "use strict";
    Ajax = tslib_1.__importStar(Ajax);
    /**
     * @deprecated 6.2 No longer in use.
     */
    class UiUserProfileMenuItemAbstract {
        _button = document.createElement("a");
        _isActive;
        _listItem = document.createElement("li");
        _userId;
        /**
         * Creates a new user profile menu item.
         */
        constructor(userId, isActive) {
            this._userId = userId;
            this._isActive = isActive;
            this._initButton();
            this._updateButton();
        }
        /**
         * Initializes the menu item.
         */
        _initButton() {
            this._button.href = "#";
            this._button.addEventListener("click", (ev) => this._toggle(ev));
            this._listItem.appendChild(this._button);
            const menu = document.querySelector(".contentInteractionDropdownItems");
            menu.insertAdjacentElement("afterbegin", this._listItem);
        }
        /**
         * Handles clicks on the menu item button.
         */
        _toggle(event) {
            event.preventDefault();
            Ajax.api(this, {
                actionName: this._getAjaxActionName(),
                parameters: {
                    data: {
                        userID: this._userId,
                    },
                },
            });
        }
        /**
         * Updates the button state and label.
         *
         * @protected
         */
        _updateButton() {
            this._button.textContent = this._getLabel();
            if (this._isActive) {
                this._listItem.classList.add("active");
            }
            else {
                this._listItem.classList.remove("active");
            }
        }
        /**
         * Returns the button label.
         */
        _getLabel() {
            // This should be an abstract method, but cannot be marked as such for backwards compatibility.
            throw new Error("Implement me!");
        }
        /**
         * Returns the Ajax action name.
         */
        _getAjaxActionName() {
            // This should be an abstract method, but cannot be marked as such for backwards compatibility.
            throw new Error("Implement me!");
        }
        /**
         * Handles successful Ajax requests.
         */
        _ajaxSuccess(_data) {
            // This should be an abstract method, but cannot be marked as such for backwards compatibility.
            throw new Error("Implement me!");
        }
        /**
         * Returns the default Ajax request data
         */
        _ajaxSetup() {
            // This should be an abstract method, but cannot be marked as such for backwards compatibility.
            throw new Error("Implement me!");
        }
    }
    return UiUserProfileMenuItemAbstract;
});
