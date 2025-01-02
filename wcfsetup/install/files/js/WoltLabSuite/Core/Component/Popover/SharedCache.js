/**
 * Shared cache for popover instances serving the same selector.
 *
 * @author Alexander Ebert
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.1
 */
define(["require", "exports", "WoltLabSuite/Core/Ajax/Backend"], function (require, exports, Backend_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.SharedCache = void 0;
    class SharedCache {
        #data = new Map();
        #callback;
        constructor(endpoint) {
            if (typeof endpoint === "string") {
                this.#callback = async (objectId) => {
                    const url = new URL(endpoint);
                    url.searchParams.set("id", objectId.toString());
                    const response = await (0, Backend_1.prepareRequest)(url).get().fetchAsResponse();
                    if (!response?.ok) {
                        return "";
                    }
                    return await response.text();
                };
            }
            else {
                this.#callback = endpoint;
            }
        }
        async get(objectId) {
            let content = this.#data.get(objectId);
            if (content !== undefined) {
                return content;
            }
            content = await this.#callback(objectId);
            this.#data.set(objectId, content);
            return content;
        }
        reset(objectId) {
            this.#data.delete(objectId);
        }
    }
    exports.SharedCache = SharedCache;
    exports.default = SharedCache;
});
