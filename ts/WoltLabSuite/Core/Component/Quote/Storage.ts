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

interface StorageData {
  quotes: Map<string, Set<string>>;
}

export const STORAGE_KEY = Core.getStoragePrefix() + "quotes";

export function saveQuote(objectType: string, objectId: number, message: string) {
  const storage = getStorage();

  const key = getKey(objectType, objectId);
  if (!storage.quotes.has(key)) {
    storage.quotes.set(key, new Set());
  }

  storage.quotes.get(key)!.add(message);

  saveStorage(storage);
}

export async function saveFullQuote(objectType: string, objectId: number) {
  const result = await renderQuote(objectType, objectId);
  if (!result.ok) {
    // TODO error handling
    return;
  }

  saveQuote(objectType, objectId, result.value);
}

export function getQuotes(): Map<string, Set<string>> {
  return getStorage().quotes;
}

function getStorage(): StorageData {
  const data = window.localStorage.getItem(STORAGE_KEY);
  if (data === null) {
    return {
      quotes: new Map(),
    };
  } else {
    return JSON.parse(data, (key, value) => {
      if (key === "quotes") {
        const result = new Map<string, Set<string>>(value);
        for (const [key, setValue] of result) {
          result.set(key, new Set(setValue));
        }
        return result;
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
