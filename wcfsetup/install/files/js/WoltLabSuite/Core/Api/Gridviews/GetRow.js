define(["require", "exports", "WoltLabSuite/Core/Ajax/Backend", "../Result"], function (require, exports, Backend_1, Result_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.getRow = getRow;
    async function getRow(gridViewClass, objectId) {
        const url = new URL(`${window.WSC_RPC_API_URL}core/grid-views/row`);
        url.searchParams.set("gridView", gridViewClass);
        url.searchParams.set("objectID", objectId.toString());
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
