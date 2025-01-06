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
  authorID: number | null;
  author: string;
  avatar: string;
}

interface Quote {
  message: string;
  rawMessage?: string;
}

interface StorageData {
  quotes: Map<string, Quote[]>;
  messages: Map<string, Message>;
}

const STORAGE_KEY = Core.getStoragePrefix() + "quotes";

export async function saveQuote(objectType: string, objectId: number, objectClassName: string, message: string) {
  const result = await messageAuthor(objectClassName, objectId);
  if (!result.ok) {
    // TODO error handling
    return;
  }

  storeQuote(objectType, result.value, {
    message,
  });

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
    {
      message: result.value.message!,
      rawMessage: result.value.rawMessage!,
    },
  );

  refreshQuoteLists();
}

export function getQuotes(): Map<string, Quote[]> {
  return getStorage().quotes;
}

export function getMessage(objectType: string, objectId?: number): Message | undefined {
  const key = objectId ? getKey(objectType, objectId) : objectType;

  return getStorage().messages.get(key);
}

export function removeQuote(key: string, index: number): void {
  const storage = getStorage();
  if (!storage.quotes.has(key)) {
    return;
  }

  storage.quotes.get(key)!.splice(index, 1);

  if (storage.quotes.get(key)!.length === 0) {
    storage.quotes.delete(key);
    storage.messages.delete(key);
  }

  saveStorage(storage);

  refreshQuoteLists();
}

function storeQuote(objectType: string, message: Message, quote: Quote): void {
  const storage = getStorage();

  const key = getKey(objectType, message.objectID);
  if (!storage.quotes.has(key)) {
    storage.quotes.set(key, []);
  }

  storage.messages.set(key, message);
  storage.quotes.get(key)!.push(quote);

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
        return new Map<string, Quote[]>(value);
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
    JSON.stringify(data, (_key, value) => {
      if (value instanceof Map) {
        return Array.from(value.entries());
      } else if (value instanceof Set) {
        return Array.from(value);
      }

      return value;
    }),
  );
}
