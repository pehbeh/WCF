/**
 * Marks all moderation queue entries as read.
 *
 * @author  Marcel Werk
 * @copyright  2001-2022 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.0
 */

import { showDefaultSuccessSnackbar } from "WoltLabSuite/Core/Component/Snackbar";
import { dboAction } from "../../Ajax";

async function markAllAsRead(): Promise<void> {
  await dboAction("markAllAsRead", "wcf\\data\\moderation\\queue\\ModerationQueueAction").dispatch();

  const gridViewTable = document.getElementById("wcf-system-gridView-user-ModerationQueueGridView_table")!;
  gridViewTable.dispatchEvent(new CustomEvent("interaction:invalidate-all"));

  showDefaultSuccessSnackbar();
}

export function setup(): void {
  document.querySelector<HTMLButtonElement>(".markAllAsReadButton")?.addEventListener("click", () => {
    void markAllAsRead();
  });
}
