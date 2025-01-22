/**
 * Requests render a full quote of a message.
 *
 * @author Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @woltlabExcludeBundle tiny
 */
define(["require", "exports", "WoltLabSuite/Core/Ajax/Backend", "../Result"], function (require, exports, Backend_1, Result_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.renderQuote = renderQuote;
    async function renderQuote(objectType, className, objectID) {
	  const url = new URL(window.WSC_RPC_API_URL + "core/messages/render-quote");
        url.searchParams.set("objectType", objectType);
        url.searchParams.set("className", className);
        url.searchParams.set("fullQuote", "true");
        url.searchParams.set("objectID", objectID.toString());
        let response;
        try {
            response = (await (0, Backend_1.prepareRequest)(url).get().fetchAsJson());
        }
        catch (e) {
            return (0, Result_1.apiResultFromError)(e);
        }
        return (0, Result_1.apiResultFromValue)(response);
    }
});
