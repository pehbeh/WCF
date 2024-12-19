/**
 * Stores the quote data.
 *
 * @author Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @woltlabExcludeBundle tiny
 */

import * as Core from "WoltLabSuite/Core/Core";
import { renderQuote } from "WoltLabSuite/Core/Api/Messages/RenderQuote";
import { messageAuthor } from "WoltLabSuite/Core/Api/Messages/Author";
import { refreshQuoteLists } from "WoltLabSuite/Core/Component/Quote/List";

interface Message {
  objectID: number;
  time: string;
  title: string;
  link: string;
  authorID: number;
  author: string;
  avatar: string;
}

interface StorageData {
  quotes: Map<string, Set<string>>;
  messages: Map<string, Message>;
}

const STORAGE_KEY = Core.getStoragePrefix() + "quotes";

export async function saveQuote(objectType: string, objectId: number, objectClassName: string, message: string) {
  const result = await messageAuthor(objectClassName, objectId);
  if (!result.ok) {
    // TODO error handling
    return;
  }

  storeQuote(objectType, result.value, message);

  refreshQuoteLists();
}

export async function saveFullQuote(objectType: string, objectClassName: string, objectId: number) {
  const result = await renderQuote(objectType, objectClassName, objectId);
  if (!result.ok) {
    // TODO error handling
    return;
  }

  storeQuote(
    objectType,
    {
      objectID: result.value.objectID,
      time: result.value.time,
      title: result.value.title,
      link: result.value.link,
      authorID: result.value.authorID,
      author: result.value.author,
      avatar: result.value.avatar,
    },
    result.value.message,
  );
}

export function getQuotes(): Map<string, Set<string>> {
  return getStorage().quotes;
}

export function getMessage(objectType: string, objectId?: number): Message | undefined {
  const key = objectId ? getKey(objectType, objectId) : objectType;

  return getStorage().messages.get(key);
}

export function removeQuote(objectType: string, objectId: number, quote: string): void {
  const storage = getStorage();

  const key = getKey(objectType, objectId);
  if (!storage.quotes.has(key)) {
    return;
  }

  storage.quotes.get(key)!.delete(quote);

  if (storage.quotes.get(key)!.size === 0) {
    storage.quotes.delete(key);
    storage.messages.delete(key);
  }

  saveStorage(storage);

  refreshQuoteLists();
}

function storeQuote(objectType: string, message: Message, quote: string): void {
  const storage = getStorage();

  const key = getKey(objectType, message.objectID);
  if (!storage.quotes.has(key)) {
    storage.quotes.set(key, new Set());
  }

  storage.messages.set(key, message);

  storage.quotes.get(key)!.add(quote);

  saveStorage(storage);
}

function getStorage(): StorageData {
  const data = window.localStorage.getItem(STORAGE_KEY);
  if (data === null) {
    return {
      quotes: new Map(),
      messages: new Map(),
    };
  } else {
    return JSON.parse(data, (key, value) => {
      if (key === "quotes") {
        const result = new Map<string, Set<string>>(value);
        for (const [key, setValue] of result) {
          result.set(key, new Set(setValue));
        }
        return result;
      } else if (key === "messages") {
        return new Map<string, Message>(value);
      }

      return value;
    });
  }
}

function getKey(objectType: string, objectId: number): string {
  return `${objectType}:${objectId}`;
}

function saveStorage(data: StorageData) {
  window.localStorage.setItem(
    STORAGE_KEY,
    JSON.stringify(data, (key, value) => {
      if (value instanceof Map) {
        return Array.from(value.entries());
      } else if (value instanceof Set) {
        return Array.from(value);
      }

      return value;
    }),
  );
}
