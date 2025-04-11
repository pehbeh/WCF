/**
 * Gets a single item for rendering in a list view.
 *
 * @author Marcel Werk
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
define(["require", "exports", "WoltLabSuite/Core/Ajax/Backend", "../Result"], function (require, exports, Backend_1, Result_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.getItem = getItem;
    async function getItem(listViewClass, objectId, listViewParameters) {
        const url = new URL(`${window.WSC_RPC_API_URL}core/list-views/item`);
        url.searchParams.set("listView", listViewClass);
        url.searchParams.set("objectID", objectId.toString());
        if (listViewParameters) {
            listViewParameters.forEach((value, key) => {
                url.searchParams.set(`listViewParameters[${key}]`, value);
            });
        }
        let response;
        try {
            response = (await (0, Backend_1.prepareRequest)(url).get().allowCaching().disableLoadingIndicator().fetchAsJson());
        }
        catch (e) {
            return (0, Result_1.apiResultFromError)(e);
        }
        return (0, Result_1.apiResultFromValue)(response);
    }
});
