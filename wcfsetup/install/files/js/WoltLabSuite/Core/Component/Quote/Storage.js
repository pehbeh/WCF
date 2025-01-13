/**
 * Stores the quote data.
 *
 * @author Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @woltlabExcludeBundle tiny
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Api/Messages/RenderQuote", "WoltLabSuite/Core/Api/Messages/Author", "WoltLabSuite/Core/Component/Quote/List"], function (require, exports, tslib_1, Core, RenderQuote_1, Author_1, List_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.saveQuote = saveQuote;
    exports.saveFullQuote = saveFullQuote;
    exports.getQuotes = getQuotes;
    exports.getMessage = getMessage;
    exports.removeQuote = removeQuote;
    Core = tslib_1.__importStar(Core);
    const STORAGE_KEY = Core.getStoragePrefix() + "quotes";
    async function saveQuote(objectType, objectId, objectClassName, message) {
        const result = await (0, Author_1.messageAuthor)(objectClassName, objectId);
        if (!result.ok) {
            // TODO error handling
            return;
        }
        storeQuote(objectType, result.value, {
            message,
        });
        (0, List_1.refreshQuoteLists)();
    }
    async function saveFullQuote(objectType, objectClassName, objectId) {
        const result = await (0, RenderQuote_1.renderQuote)(objectType, objectClassName, objectId);
        if (!result.ok) {
            // TODO error handling
            return;
        }
        storeQuote(objectType, {
            objectID: result.value.objectID,
            time: result.value.time,
            title: result.value.title,
            link: result.value.link,
            authorID: result.value.authorID,
            author: result.value.author,
            avatar: result.value.avatar,
        }, {
            message: result.value.message,
            rawMessage: result.value.rawMessage,
        });
        (0, List_1.refreshQuoteLists)();
    }
    function getQuotes() {
        return getStorage().quotes;
    }
    function getMessage(objectType, objectId) {
        const key = objectId ? getKey(objectType, objectId) : objectType;
        return getStorage().messages.get(key);
    }
    function removeQuote(key, quote) {
        const storage = getStorage();
        if (!storage.quotes.has(key)) {
            return;
        }
        storage.quotes.get(key).forEach((q) => {
            if (JSON.stringify(q) === JSON.stringify(quote)) {
                storage.quotes.get(key).delete(q);
            }
        });
        if (storage.quotes.get(key).size === 0) {
            storage.quotes.delete(key);
            storage.messages.delete(key);
        }
        saveStorage(storage);
        (0, List_1.refreshQuoteLists)();
    }
    function storeQuote(objectType, message, quote) {
        const storage = getStorage();
        const key = getKey(objectType, message.objectID);
        if (!storage.quotes.has(key)) {
            storage.quotes.set(key, new Set());
        }
        storage.messages.set(key, message);
        if (!Array.from(storage.quotes.get(key))
            .map((q) => JSON.stringify(q))
            .includes(JSON.stringify(quote))) {
            storage.quotes.get(key).add(quote);
        }
        saveStorage(storage);
    }
    function getStorage() {
        const data = window.localStorage.getItem(STORAGE_KEY);
        if (data === null) {
            return {
                quotes: new Map(),
                messages: new Map(),
            };
        }
        else {
            return JSON.parse(data, (key, value) => {
                if (key === "quotes") {
                    const result = new Map(value);
                    for (const [key, setValue] of result) {
                        result.set(key, new Set(setValue));
                    }
                    return result;
                }
                else if (key === "messages") {
                    return new Map(value);
                }
                return value;
            });
        }
    }
    function getKey(objectType, objectId) {
        return `${objectType}:${objectId}`;
    }
    function saveStorage(data) {
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(data, (_key, value) => {
            if (value instanceof Map) {
                return Array.from(value.entries());
            }
            else if (value instanceof Set) {
                return Array.from(value);
            }
            return value;
        }));
    }
});
