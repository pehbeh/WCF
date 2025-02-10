/**
 * Gets the rows for the rendering of a grid view that can be sorted.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
define(["require", "exports", "WoltLabSuite/Core/Ajax/Backend", "../Result"], function (require, exports, Backend_1, Result_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.getSortDialog = getSortDialog;
    async function getSortDialog(gridViewClass, filters, gridViewParameters) {
        const url = new URL(`${window.WSC_RPC_API_URL}core/grid-views/sort`);
        url.searchParams.set("gridView", gridViewClass);
        if (filters) {
            filters.forEach((value, key) => {
                url.searchParams.set(`filters[${key}]`, value);
            });
        }
        if (gridViewParameters) {
            gridViewParameters.forEach((value, key) => {
                url.searchParams.set(`gridViewParameters[${key}]`, value);
            });
        }
        let response;
        try {
            response = (await (0, Backend_1.prepareRequest)(url).get().allowCaching().disableLoadingIndicator().fetchAsJson());
        }
        catch (e) {
            return (0, Result_1.apiResultFromError)(e);
        }
        return (0, Result_1.apiResultFromValue)(response.template);
    }
});
