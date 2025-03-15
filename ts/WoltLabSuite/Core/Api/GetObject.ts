/**
 * Sends a get request to the given endpoint.
 *
 * @author Marcel Werk
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 * @woltlabExcludeBundle tiny
 */

import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { ApiResult, apiResultFromError, apiResultFromValue } from "./Result";

export async function getObject<T = unknown>(endpoint: string): Promise<ApiResult<T>> {
  let response: T;

  try {
    response = (await prepareRequest(endpoint).get().fetchAsJson()) as T;
  } catch (e) {
    return apiResultFromError(e);
  }

  return apiResultFromValue(response);
}
