/**
 * Marks all moderation queue entries as read.
 *
 * @author  Marcel Werk
 * @copyright  2001-2022 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.0
 */

import { dboAction } from "../../Ajax";
import * as UiNotification from "../Notification";

async function markAllAsRead(): Promise<void> {
  await dboAction("markAllAsRead", "wcf\\data\\moderation\\queue\\ModerationQueueAction").dispatch();

  document
    .querySelectorAll("#wcf-system-gridView-user-ModerationQueueGridView_table .newMessageBadge")
    .forEach((el: HTMLElement) => {
      el.remove();
    });
  document.querySelector("#outstandingModeration .badgeUpdate")?.remove();

  UiNotification.show();
}

export function setup(): void {
  document.querySelector<HTMLButtonElement>(".markAllAsReadButton")?.addEventListener("click", (event) => {
    event.preventDefault();

    void markAllAsRead();
  });
}
