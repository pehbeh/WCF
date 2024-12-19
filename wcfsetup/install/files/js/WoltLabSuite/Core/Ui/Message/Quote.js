/**
 * @woltlabExcludeBundle tiny
 *
 * @deprecated 6.2 use `WoltLabSuite/Core/Component/Quote/Message` instead
 */
define(["require", "exports", "WoltLabSuite/Core/Component/Quote/Message"], function (require, exports, Message_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.UiMessageQuote = void 0;
    class UiMessageQuote {
        /**
         * Initializes the quote handler for given object type.
         */
        constructor(quoteManager, className, objectType, containerSelector, messageBodySelector, messageContentSelector, supportDirectInsert) {
            (0, Message_1.registerContainer)(containerSelector, messageBodySelector, objectType);
        }
    }
    exports.UiMessageQuote = UiMessageQuote;
    exports.default = UiMessageQuote;
});
