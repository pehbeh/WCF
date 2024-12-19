/**
 * Handles quotes for CKEditor 5 message fields.
 *
 * @author Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @woltlabExcludeBundle tiny
 */
define(["require", "exports", "WoltLabSuite/Core/Component/Ckeditor/Event", "WoltLabSuite/Core/Component/Message/MessageTabMenu", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Component/Quote/Message", "WoltLabSuite/Core/Component/Quote/Storage"], function (require, exports, Event_1, MessageTabMenu_1, Language_1, Message_1, Storage_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.getQuoteList = getQuoteList;
    exports.setup = setup;
    const quoteLists = new Map();
    class QuoteList {
        #container;
        #editor;
        #editorId;
        constructor(editorId, editor) {
            this.#editorId = editorId;
            this.#editor = editor;
            this.#container = document.getElementById(`quotes_${editorId}`);
            if (this.#container === null) {
                throw new Error(`The quotes container for '${editorId}' does not exist.`);
            }
            window.addEventListener("storage", () => {
                this.renderQuotes();
            });
            this.renderQuotes();
        }
        renderQuotes() {
            this.#container.innerHTML = "";
            for (const [, quotes] of (0, Storage_1.getQuotes)()) {
                // TODO render quotes
            }
            if (this.#container.hasChildNodes()) {
                (0, MessageTabMenu_1.getTabMenu)(this.#editorId)?.showTab("quotes", (0, Language_1.getPhrase)("wcf.message.quote.showQuotes", {
                    count: this.#container.childElementCount,
                }));
            }
            else {
                (0, MessageTabMenu_1.getTabMenu)(this.#editorId)?.hideTab("quotes");
            }
        }
    }
    function getQuoteList(editorId) {
        return quoteLists.get(editorId);
    }
    function setup(editorId) {
        if (quoteLists.has(editorId)) {
            return;
        }
        const editor = document.getElementById(editorId);
        if (editor === null) {
            throw new Error(`The editor '${editorId}' does not exist.`);
        }
        (0, Event_1.listenToCkeditor)(editor).ready(({ ckeditor }) => {
            if (ckeditor.features.quoteBlock) {
                quoteLists.set(editorId, new QuoteList(editorId, ckeditor));
            }
            (0, Message_1.setActiveEditor)(ckeditor, ckeditor.features.quoteBlock);
            ckeditor.focusTracker.on("change:isFocused", (_evt, _name, isFocused) => {
                if (isFocused) {
                    (0, Message_1.setActiveEditor)(ckeditor, ckeditor.features.quoteBlock);
                }
            });
        });
    }
});
