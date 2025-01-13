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
import { getQuotes, getMessage, removeQuote } from "WoltLabSuite/Core/Component/Quote/Storage";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import { escapeHTML } from "WoltLabSuite/Core/StringUtil";

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

      quotes.forEach((quote) => {
        const fragment = DomUtil.createFragmentFromHtml(`
<div class="quoteBox quoteBox--tabMenu">
  <div class="quoteBoxIcon">
    <img src="${escapeHTML(message.avatar)}" alt="" class="userAvatarImage" height="24" width="24">
  </div>
  <div class="quoteBoxTitle">
    <a href="${escapeHTML(message.link)}" target="_blank">${escapeHTML(message.author)}</a>
  </div>
  <div class="quoteBoxButtons">
    <button type="button" class="button small jsTooltip" title="${getPhrase("wcf.global.button.delete")}" data-action="delete">
      <fa-icon name="times"></fa-icon>
    </button>
    <button type="button" class="button buttonPrimary small jsTooltip" title="${getPhrase("wcf.message.quote.insertQuote")}" data-action="insert">
      <fa-icon name="paste"></fa-icon>
    </button>
  </div>
  <div class="quoteBoxContent">
    ${quote.rawMessage === undefined ? quote.message : quote.rawMessage}
  </div>
</div>
        `);

        fragment.querySelector('button[data-action="insert"]')!.addEventListener("click", () => {
          dispatchToCkeditor(this.#editor).insertQuote({
            author: message.author,
            content: quote.rawMessage === undefined ? quote.message : quote.rawMessage,
            isText: quote.rawMessage === undefined,
            link: message.link,
          });
        });

        fragment.querySelector('button[data-action="delete"]')!.addEventListener("click", () => {
          removeQuote(key, quote);
        });

        this.#container.append(fragment);
      });
    }

    const tabMenu = getTabMenu(this.#editorId);
    if (tabMenu === undefined) {
      throw new Error(`Could not find the tab menu for '${this.#editorId}'.`);
    }

    tabMenu.setTabCounter("quotes", quotesCount);

    if (quotesCount > 0) {
      tabMenu.showTab("quotes");
    } else {
      tabMenu.hideTab("quotes");
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
