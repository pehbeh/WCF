import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { ApiResult, apiResultFromError, apiResultFromValue } from "../Result";

type Response = {
  template: string;
};

export async function getBulkContextMenuOptions(
  providerClassName: string,
  objectIds: number[],
): Promise<ApiResult<Response>> {
  let response: Response;
  try {
    response = (await prepareRequest(`${window.WSC_RPC_API_URL}core/interactions/bulk-context-menu-options`)
      .post({ provider: providerClassName, objectIDs: objectIds })
      .disableLoadingIndicator()
      .fetchAsJson()) as Response;
  } catch (e) {
    return apiResultFromError(e);
  }

  return apiResultFromValue(response);
}
