/**
 * Handles quotes for CKEditor 5 message fields.
 *
 * @author Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @woltlabExcludeBundle tiny
 */

import { listenToCkeditor, dispatchToCkeditor } from "WoltLabSuite/Core/Component/Ckeditor/Event";
import { getTabMenu } from "WoltLabSuite/Core/Component/Message/MessageTabMenu";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { setActiveEditor } from "WoltLabSuite/Core/Component/Quote/Message";
import { getQuotes, getMessage } from "WoltLabSuite/Core/Component/Quote/Storage";
import DomUtil from "WoltLabSuite/Core/Dom/Util";

const quoteLists = new Map<string, QuoteList>();

class QuoteList {
  #container: HTMLElement;
  #editor: HTMLElement;
  #editorId: string;

  constructor(editorId: string, editor: HTMLElement) {
    this.#editorId = editorId;
    this.#editor = editor;
    this.#container = document.getElementById(`quotes_${editorId}`)!;
    if (this.#container === null) {
      throw new Error(`The quotes container for '${editorId}' does not exist.`);
    }

    window.addEventListener("storage", () => {
      this.renderQuotes();
    });

    this.renderQuotes();
  }

  public renderQuotes(): void {
    this.#container.innerHTML = "";

    let quotesCount = 0;
    for (const [key, quotes] of getQuotes()) {
      const message = getMessage(key)!;
      quotesCount += quotes.size;

      // TODO escape values
      // TODO create web components???
      const fragment = DomUtil.createFragmentFromHtml(`<article class="message messageReduced jsInvalidQuoteTarget">
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
          .map(
            (quote) => `<li>
  <span>
    <input type="checkbox" value="1" class="jsCheckbox">
    <button type="button" class="jsTooltip jsInsertQuote" title="${getPhrase("wcf.message.quote.insertQuote")}">
        <fa-icon name="plus"></fa-icon>
    </button>
  </span>
  
  <div class="jsQuote">
    ${quote.message}
  </div>
</li>`,
          )
          .join("")}
        </ul>
      </div>
    </div>
  </div>
</article>`);

      fragment.querySelectorAll<HTMLButtonElement>(".jsInsertQuote").forEach((button) => {
        button.addEventListener("click", () => {
          // TODO dont query the DOM
          // TODO use rawMessage to insert if available otherwise use message
          dispatchToCkeditor(this.#editor).insertQuote({
            author: message.author,
            content: button.closest("li")!.querySelector(".jsQuote")!.innerHTML,
            isText: false,
            link: message.link,
          });
        });
      });

      this.#container.append(fragment);
    }

    if (quotesCount > 0) {
      getTabMenu(this.#editorId)?.showTab(
        "quotes",
        getPhrase("wcf.message.quote.showQuotes", {
          count: quotesCount,
        }),
      );
    } else {
      getTabMenu(this.#editorId)?.hideTab("quotes");
    }
  }
}

export function getQuoteList(editorId: string): QuoteList | undefined {
  return quoteLists.get(editorId);
}

export function refreshQuoteLists() {
  for (const quoteList of quoteLists.values()) {
    quoteList.renderQuotes();
  }
}

export function setup(editorId: string): void {
  if (quoteLists.has(editorId)) {
    return;
  }

  const editor = document.getElementById(editorId);
  if (editor === null) {
    throw new Error(`The editor '${editorId}' does not exist.`);
  }

  listenToCkeditor(editor).ready(({ ckeditor }) => {
    if (ckeditor.features.quoteBlock) {
      quoteLists.set(editorId, new QuoteList(editorId, editor));
    }

    setActiveEditor(ckeditor, ckeditor.features.quoteBlock);

    ckeditor.focusTracker.on("change:isFocused", (_evt: unknown, _name: unknown, isFocused: boolean) => {
      if (isFocused) {
        setActiveEditor(ckeditor, ckeditor.features.quoteBlock);
      }
    });
  });
}
