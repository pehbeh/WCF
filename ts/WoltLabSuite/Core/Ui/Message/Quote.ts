/**
 * @woltlabExcludeBundle tiny
 *
 * @deprecated 6.2 use `WoltLabSuite/Core/Component/Quote/Message` instead
 */

import { registerContainer } from "WoltLabSuite/Core/Component/Quote/Message";

// see WCF.Message.Quote.Manager
export interface WCFMessageQuoteManager {
  supportPaste: () => boolean;
  updateCount: (number, object) => void;
}

export class UiMessageQuote {
  /**
   * Initializes the quote handler for given object type.
   */
  constructor(
    quoteManager: WCFMessageQuoteManager,
    className: string,
    objectType: string,
    containerSelector: string,
    messageBodySelector: string,
    messageContentSelector: string,
    supportDirectInsert: boolean,
  ) {
    registerContainer(containerSelector, messageBodySelector);
  }
}

export default UiMessageQuote;
