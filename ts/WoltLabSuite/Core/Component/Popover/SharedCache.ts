/**
 * Shared cache for popover instances serving the same selector.
 *
 * @author Alexander Ebert
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.1
 */

import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";

type ObjectId = number;

export class SharedCache {
  readonly #data = new Map<ObjectId, string>();
  readonly #callback: (objectId: number) => Promise<string>;

  constructor(endpoint: string | ((objectId: number) => Promise<string>)) {
    if (typeof endpoint === "string") {
      this.#callback = async (objectId: number) => {
        const url = new URL(endpoint);
        url.searchParams.set("id", objectId.toString());
        const response = await prepareRequest(url).get().fetchAsResponse();
        if (!response?.ok) {
          return "";
        }

        return await response.text();
      };
    } else {
      this.#callback = endpoint;
    }
  }

  async get(objectId: ObjectId): Promise<string> {
    let content = this.#data.get(objectId);
    if (content !== undefined) {
      return content;
    }

    content = await this.#callback(objectId);
    this.#data.set(objectId, content);

    return content;
  }

  reset(objectId: ObjectId): void {
    this.#data.delete(objectId);
  }
}

export default SharedCache;
