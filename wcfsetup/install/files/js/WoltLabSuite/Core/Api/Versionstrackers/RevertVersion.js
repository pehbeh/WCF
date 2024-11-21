/**
 * Reverts a version tracker object to a previous version.
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @woltlabExcludeBundle tiny
 */
define(["require", "exports", "WoltLabSuite/Core/Ajax/Backend", "../Result"], function (require, exports, Backend_1, Result_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.revertVersion = revertVersion;
    async function revertVersion(objectType, objectId, versionId) {
        try {
            await (0, Backend_1.prepareRequest)(`${window.WSC_RPC_API_URL}core/versiontrackers/revert`)
                .post({
                objectType,
                objectId,
                versionId,
            })
                .fetchAsJson();
        }
        catch (e) {
            return (0, Result_1.apiResultFromError)(e);
        }
        return (0, Result_1.apiResultFromValue)([]);
    }
});
