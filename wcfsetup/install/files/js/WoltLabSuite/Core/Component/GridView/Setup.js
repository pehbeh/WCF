/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
define(["require", "exports", "WoltLabSuite/Core/Ajax/Backend"], function (require, exports, Backend_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.GridViewSetup = void 0;
    class GridViewSetup {
        async fromGridView(title, gridViewClass, pageNo = 1, sortField = "", sortOrder = "ASC", filters, gridViewParameters) {
            const url = new URL(`${window.WSC_RPC_API_URL}core/grid-views/render`);
            url.searchParams.set("gridView", gridViewClass);
            url.searchParams.set("pageNo", pageNo.toString());
            url.searchParams.set("sortField", sortField);
            url.searchParams.set("sortOrder", sortOrder);
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
            const json = (await (0, Backend_1.prepareRequest)(url).get().fetchAsJson());
            // Prevents a circular dependency.
            const { dialogFactory } = await new Promise((resolve_1, reject_1) => { require(["../Dialog"], resolve_1, reject_1); }).then(__importStar);
            const dialog = dialogFactory().fromHtml(json.gridView).withoutControls();
            dialog.show(title);
            return dialog;
        }
    }
    exports.GridViewSetup = GridViewSetup;
    exports.default = GridViewSetup;
});
