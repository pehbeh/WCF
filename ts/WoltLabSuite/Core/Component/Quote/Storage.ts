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

interface Message {
  objectID: number;
  time: number;
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
      link: result.value.link,
      authorID: result.value.authorID,
      author: result.value.author,
      avatar: result.value.avatar,
    },
    result.value.message,
  );
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

export function getQuotes(): Map<string, Set<string>> {
  return getStorage().quotes;
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
