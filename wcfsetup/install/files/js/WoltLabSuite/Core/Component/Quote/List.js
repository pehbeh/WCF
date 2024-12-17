define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Component/Ckeditor/Event", "WoltLabSuite/Core/Component/Message/MessageTabMenu", "WoltLabSuite/Core/Language"], function (require, exports, tslib_1, Core, Event_1, MessageTabMenu_1, Language_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.STORAGE_KEY = void 0;
    exports.getQuoteList = getQuoteList;
    exports.setup = setup;
    Core = tslib_1.__importStar(Core);
    exports.STORAGE_KEY = Core.getStoragePrefix() + "quotes";
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
            window.addEventListener("storage", (event) => {
                if (event.key !== exports.STORAGE_KEY) {
                    return;
                }
                this.renderQuotes(event.newValue);
            });
            this.renderQuotes(window.localStorage.getItem(exports.STORAGE_KEY));
        }
        renderQuotes(template) {
            this.#container.innerHTML = template || "";
            if (template) {
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
        });
    }
});
