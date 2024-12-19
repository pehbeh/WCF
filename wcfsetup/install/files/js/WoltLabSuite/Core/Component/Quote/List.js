/**
 * Handles quotes for CKEditor 5 message fields.
 *
 * @author Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @woltlabExcludeBundle tiny
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Component/Ckeditor/Event", "WoltLabSuite/Core/Component/Message/MessageTabMenu", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Component/Quote/Message", "WoltLabSuite/Core/Component/Quote/Storage", "WoltLabSuite/Core/Dom/Util"], function (require, exports, tslib_1, Event_1, MessageTabMenu_1, Language_1, Message_1, Storage_1, Util_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.getQuoteList = getQuoteList;
    exports.setup = setup;
    Util_1 = tslib_1.__importDefault(Util_1);
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
            for (const [key, quotes] of (0, Storage_1.getQuotes)()) {
                const message = (0, Storage_1.getMessage)(key);
                // TODO escape values
                // TODO create web components???
                this.#container.append(Util_1.default.createFragmentFromHtml(`<article class="message messageReduced jsInvalidQuoteTarget">
  <div class="messageContent">
    <header class="messageHeader">
      <div class="box32 messageHeaderWrapper">
        <!-- TODO load real avatar -->
        <span><img src="${window.WCF_PATH}images/avatars/avatar-default.svg" alt="" class="userAvatarImage" style="width: 32px; height: 32px"></span>
        <div class="messageHeaderBox">
          <h2 class="messageTitle">
            <a href="${message.link}">${message.title}</a>
          </h2>
          <ul class="messageHeaderMetaData">
            <!-- TODO add link to author profile -->
            <li><span class="username">${message.author}</span></li>
            <li><span class="messagePublicationTime"><woltlab-core-date-time date="${message.time}">${message.time}</woltlab-core-date-time></span></li>
          </ul>
        </div>
      </div>
    </header>
    <div class="messageBody">
      <div class="messageText">
        <ul class="messageQuoteItemList">
        ${Array.from(quotes)
                    .map((quote) => `<li>
  <span>
    <input type="checkbox" value="1" class="jsCheckbox">
    <button type="button" class="jsTooltip jsInsertQuote" title="${(0, Language_1.getPhrase)("wcf.message.quote.insertQuote")}">
    </button>
  </span>
  
  <div class="jsQuote">
  <label for="quote_{@$quoteID}">
    ${quote}
  </label>
  </div>
</li>`)
                    .join("")}
        </ul>
      </div>
    </div>
  </div>
</article>`));
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
