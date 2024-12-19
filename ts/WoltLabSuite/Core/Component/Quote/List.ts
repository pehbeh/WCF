/**
 * Handles quotes for CKEditor 5 message fields.
 *
 * @author Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @woltlabExcludeBundle tiny
 */

import { listenToCkeditor } from "WoltLabSuite/Core/Component/Ckeditor/Event";
import type { CKEditor } from "WoltLabSuite/Core/Component/Ckeditor";
import { getTabMenu } from "WoltLabSuite/Core/Component/Message/MessageTabMenu";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { setActiveEditor } from "WoltLabSuite/Core/Component/Quote/Message";
import { getQuotes } from "WoltLabSuite/Core/Component/Quote/Storage";

const quoteLists = new Map<string, QuoteList>();

class QuoteList {
  #container: HTMLElement;
  #editor: CKEditor;
  #editorId: string;

  constructor(editorId: string, editor: CKEditor) {
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

    for (const [, quotes] of getQuotes()) {
      // TODO render quotes
    }

    if (this.#container.hasChildNodes()) {
      getTabMenu(this.#editorId)?.showTab(
        "quotes",
        getPhrase("wcf.message.quote.showQuotes", {
          count: this.#container.childElementCount,
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
      quoteLists.set(editorId, new QuoteList(editorId, ckeditor));
    }

    setActiveEditor(ckeditor, ckeditor.features.quoteBlock);

    ckeditor.focusTracker.on("change:isFocused", (_evt: unknown, _name: unknown, isFocused: boolean) => {
      if (isFocused) {
        setActiveEditor(ckeditor, ckeditor.features.quoteBlock);
      }
    });
  });
}
