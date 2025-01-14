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
  quotes: Map<string, Map<string, Quote>>;
  messages: Map<string, Message>;
}

const STORAGE_KEY = Core.getStoragePrefix() + "quotes";
const usedQuotes = new Map<string, Set<string>>();

export async function saveQuote(
  objectType: string,
  objectId: number,
  objectClassName: string,
  message: string,
): Promise<Message & Quote & { uuid: string }> {
  const result = await messageAuthor(objectClassName, objectId);
  if (!result.ok) {
    throw new Error("Error fetching author data");
  }

  const uuid = storeQuote(objectType, result.value, {
    message,
  });

  refreshQuoteLists();

  return {
    ...result.value,
    message,
    uuid,
  };
}

export async function saveFullQuote(
  objectType: string,
  objectClassName: string,
  objectId: number,
): Promise<Message & Quote & { uuid: string }> {
  const result = await renderQuote(objectType, objectClassName, objectId);
  if (!result.ok) {
    throw new Error("Error fetching quote data");
  }

  const message = {
    objectID: result.value.objectID,
    time: result.value.time,
    title: result.value.title,
    link: result.value.link,
    authorID: result.value.authorID,
    author: result.value.author,
    avatar: result.value.avatar,
  };

  const quote = {
    message: result.value.message!,
    rawMessage: result.value.rawMessage!,
  };

  const uuid = storeQuote(objectType, message, quote);

  refreshQuoteLists();

  return {
    ...message,
    ...quote,
    uuid,
  };
}

export function getQuotes(): Map<string, Map<string, Quote>> {
  return getStorage().quotes;
}

export function getMessage(objectType: string, objectId?: number): Message | undefined {
  const key = objectId ? getKey(objectType, objectId) : objectType;

  return getStorage().messages.get(key);
}

export function removeQuote(key: string, uuid: string): void {
  const storage = getStorage();
  if (!storage.quotes.has(key)) {
    return;
  }

  storage.quotes.get(key)!.delete(uuid);

  if (storage.quotes.get(key)!.size === 0) {
    storage.quotes.delete(key);
    storage.messages.delete(key);
  }

  saveStorage(storage);

  refreshQuoteLists();
}

export function markQuoteAsUsed(editorId: string, uuid: string): void {
  if (!usedQuotes.has(editorId)) {
    usedQuotes.set(editorId, new Set());
  }

  usedQuotes.get(editorId)!.add(uuid);
}

export function getUsedQuotes(editorId: string): Set<string> {
  return usedQuotes.get(editorId) ?? new Set();
}

export function clearQuotesForEditor(editorId: string): void {
  const storage = getStorage();

  usedQuotes.get(editorId)?.forEach((uuid) => {
    for (const quotes of storage.quotes.values()) {
      quotes.delete(uuid);
    }
  });

  usedQuotes.delete(editorId);

  for (const [key, quotes] of storage.quotes) {
    if (quotes.size === 0) {
      storage.quotes.delete(key);
      storage.messages.delete(key);
    }
  }

  saveStorage(storage);
  refreshQuoteLists();
}

export function isFullQuoted(objectType: string, objectId: number): boolean {
  const key = getKey(objectType, objectId);
  const storage = getStorage();
  const quotes = storage.quotes.get(key);

  if (quotes === undefined) {
    return false;
  }

  return (
    Array.from(quotes).filter(([, quote]) => {
      if (quote.rawMessage !== undefined) {
        return true;
      }
    }).length > 0
  );
}

function storeQuote(objectType: string, message: Message, quote: Quote): string {
  const storage = getStorage();

  const key = getKey(objectType, message.objectID);
  if (!storage.quotes.has(key)) {
    storage.quotes.set(key, new Map());
  }

  storage.messages.set(key, message);

  for (const [uuid, q] of storage.quotes.get(key)!) {
    if (JSON.stringify(q) === JSON.stringify(quote)) {
      return uuid;
    }
  }

  const uuid = Core.getUuid();
  storage.quotes.get(key)!.set(uuid, quote);

  saveStorage(storage);

  return uuid;
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
        const result = new Map<string, Map<string, Quote>>(value);
        for (const [key, quotes] of result) {
          result.set(key, new Map(quotes));
        }

        return result;
      } else if (key === "messages") {
        return new Map<string, Message>(value);
      }

      return value;
    });
  }
}

export function getKey(objectType: string, objectId: number): string {
  return `${objectType}:${objectId}`;
}

function saveStorage(data: StorageData) {
  window.localStorage.setItem(
    STORAGE_KEY,
    JSON.stringify(data, (_key, value) => {
      if (value instanceof Map) {
        return Array.from(value.entries());
      }

      return value;
    }),
  );
}
