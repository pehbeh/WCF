/**
 * Shows the activity point list for users.
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */

import { dboAction } from "WoltLabSuite/Core/Ajax";
import { promiseMutex } from "WoltLabSuite/Core/Helper/PromiseMutex";
import { wheneverFirstSeen } from "WoltLabSuite/Core/Helper/Selector";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { getPhrase } from "WoltLabSuite/Core/Language";

type ResponseGetDetailedActivityPointList = {
  template: string;
};

async function showDialog(userId: number): Promise<void> {
  const response = (await dboAction("getDetailedActivityPointList", "wcf\\data\\user\\UserProfileAction")
    .objectIds([userId])
    .dispatch()) as ResponseGetDetailedActivityPointList;

  const dialog = dialogFactory().fromHtml(response.template).withoutControls();
  dialog.show(getPhrase("wcf.user.activityPoint"));
}

export function setup(): void {
  wheneverFirstSeen(".activityPointsDisplay", (button) => {
    button.addEventListener(
      "click",
      promiseMutex((event) => {
        event.preventDefault();
        return showDialog(parseInt(button.dataset.userId!));
      }),
    );
  });
}
