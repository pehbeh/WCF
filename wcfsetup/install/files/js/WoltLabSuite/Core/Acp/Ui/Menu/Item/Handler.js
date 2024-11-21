/**
 * Provides the interface logic to add and edit menu items.
 *
 * @author  Alexander Ebert
 * @copyright  2001-2019 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
define(["require", "exports", "tslib", "../../../../Ui/Page/Search/Handler", "WoltLabSuite/Core/Language"], function (require, exports, tslib_1, UiPageSearchHandler, Language_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.AcpUiMenuItemHandler = void 0;
    UiPageSearchHandler = tslib_1.__importStar(UiPageSearchHandler);
    class AcpUiMenuItemHandler {
        #handlers;
        #identifier;
        #pageId;
        #pageObjectId;
        /**
         * Initializes the interface logic.
         */
        constructor(fieldPrefix, handlers, identifier) {
            this.#handlers = handlers;
            this.#identifier = identifier;
            if (this.#handlers.size) {
                this.#pageId = document.getElementById("pageID");
                this.#pageObjectId = document.getElementById(fieldPrefix);
                const searchButton = document.getElementById(fieldPrefix + "Search");
                searchButton.addEventListener("click", () => this.openSearch());
            }
        }
        /**
         * Opens the handler lookup dialog.
         */
        openSearch() {
            const selectedOption = this.#pageId.options[this.#pageId.selectedIndex];
            const pageIdentifier = this.#identifier.get(parseInt(selectedOption.value));
            const languageItem = `wcf.page.pageObjectID.search.${pageIdentifier}`;
            let labelLanguageItem;
            if ((0, Language_1.getPhrase)(languageItem) !== languageItem) {
                labelLanguageItem = languageItem;
            }
            UiPageSearchHandler.open(parseInt(selectedOption.value), selectedOption.textContent.trim(), (objectId) => {
                this.#pageObjectId.value = objectId.toString();
            }, labelLanguageItem);
        }
    }
    exports.AcpUiMenuItemHandler = AcpUiMenuItemHandler;
});
