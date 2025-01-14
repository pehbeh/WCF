/**
 * Requests to reset the removal quotes.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @woltlabExcludeBundle tiny
 */

import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { ApiResult, apiResultFromError, apiResultFromValue } from "../Result";

export async function resetRemovalQuotes(): Promise<ApiResult<[]>> {
  const url = new URL(window.WSC_RPC_API_URL + "core/messages/reset-removal-quotes");

  try {
    await prepareRequest(url).post().fetchAsJson();
  } catch (e) {
    return apiResultFromError(e);
  }

  return apiResultFromValue([]);
}
