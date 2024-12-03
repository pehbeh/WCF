/**
 * Handles the dialog to copy a user group.
 *
 * @author  Olaf Braun
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */

import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";

interface CopyResponse {
  groupID: number;
  redirectURL: string;
}

export function init() {
  const button = document.querySelector<HTMLElement>(".jsButtonUserGroupCopy");
  button?.addEventListener("click", () => {
    void dialogFactory()
      .usingFormBuilder()
      .fromEndpoint<CopyResponse>(button.dataset.endpoint!)
      .then((result) => {
        if (result.ok) {
          window.location.href = result.result.redirectURL;
        }
      });
  });
}
