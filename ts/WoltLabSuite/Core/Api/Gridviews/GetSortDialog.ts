/**
 * Gets the rows for the rendering of a grid view that can be sorted.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */

import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { ApiResult, apiResultFromError, apiResultFromValue } from "../Result";

type Response = {
  template?: string;
};

export async function getSortDialog(
  gridViewClass: string,
  filters?: Map<string, string>,
  gridViewParameters?: Map<string, string>,
): Promise<ApiResult<undefined | string>> {
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

  let response: Response;
  try {
    response = (await prepareRequest(url).get().allowCaching().disableLoadingIndicator().fetchAsJson()) as Response;
  } catch (e) {
    return apiResultFromError(e);
  }

  return apiResultFromValue(response.template);
}
