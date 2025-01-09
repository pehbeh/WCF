import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { ApiResult, apiResultFromError, apiResultFromValue } from "../Result";

type Response = {
  template: string;
};

export async function getContextMenuOptions(
  providerClassName: string,
  objectId: number | string,
): Promise<ApiResult<Response>> {
  const url = new URL(`${window.WSC_RPC_API_URL}core/interactions/context-menu-options`);
  url.searchParams.set("provider", providerClassName);
  url.searchParams.set("objectID", objectId.toString());

  let response: Response;
  try {
    response = (await prepareRequest(url).get().allowCaching().disableLoadingIndicator().fetchAsJson()) as Response;
  } catch (e) {
    return apiResultFromError(e);
  }

  return apiResultFromValue(response);
}
