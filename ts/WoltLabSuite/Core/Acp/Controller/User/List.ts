/**
 * Handles the list of users.
 *
 * @author  Olaf Braun
 * @copyright  2001-2024 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */

import * as EventHandler from "../../../Event/Handler";
import { ClipboardActionData } from "WoltLabSuite/Core/Controller/Clipboard/Data";
import { dboAction } from "WoltLabSuite/Core/Ajax";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import UiDropdownSimple from "WoltLabSuite/Core/Ui/Dropdown/Simple";
import BanHandler from "WoltLabSuite/Core/Acp/Ui/User/Action/Handler/Ban";
import SendNewPassword from "WoltLabSuite/Core/Acp/Ui/User/Action/Handler/SendNewPassword";
import { setup as setupClipboard, unmark as unmarkClipboard } from "WoltLabSuite/Core/Controller/Clipboard";
import AcpUiUserList from "WoltLabSuite/Core/Acp/Ui/User/Editor";
import { AcpUserContentRemoveClipboard } from "WoltLabSuite/Core/Acp/Ui/User/Content/Remove/Clipboard";

function getUserElements(userIDs: number[]): HTMLElement[] {
  return Array.from(document.querySelectorAll<HTMLElement>(".jsUserRow")).filter((userRow) =>
    userIDs.includes(parseInt(userRow.dataset.objectId!)),
  );
}

function getDropdownMenu(userRow: HTMLElement): HTMLElement {
  const userId = ~~userRow.dataset.objectId!;
  const dropdownId = `userListDropdown${userId}`;
  return UiDropdownSimple.getDropdownMenu(dropdownId)!;
}

function refresh(userIDs: number[]) {
  unmarkClipboard("com.woltlab.wcf.user", userIDs);

  showNotification();

  EventHandler.fire("com.woltlab.wcf.acp.user", "refresh", {
    userIds: userIDs,
  });
}

async function enableUsers(userIDs: number[]) {
  await dboAction("enable", "wcf\\data\\user\\UserAction").objectIds(userIDs).dispatch();

  getUserElements(userIDs).forEach((userRow) => {
    userRow.dataset.enabled = "true";
    const button = getDropdownMenu(userRow).querySelector<HTMLElement>(".jsEnable")!;
    button.textContent = button.dataset.disableMessage!;
  });

  refresh(userIDs);
}

function banUsers(userIDs: number[]) {
  new BanHandler(userIDs).ban(() => {
    getUserElements(userIDs).forEach((userRow) => {
      userRow.dataset.banned = "true";

      const button = getDropdownMenu(userRow).querySelector<HTMLElement>(".jsBan")!;
      button.textContent = button.dataset.unbanMessage!;

      refresh(userIDs);
    });
  });
}

function sendNewPasswords(userIDs: number[]) {
  new SendNewPassword(userIDs, () => {
    refresh(userIDs);
  }).send();
}

function setupUserClipboard(hasMarkedItems: boolean) {
  setupClipboard({
    pageClassName: "wcf\\acp\\page\\UserListPage",
    hasMarkedItems: hasMarkedItems,
  });

  EventHandler.add("com.woltlab.wcf.clipboard", "com.woltlab.wcf.user", (data: { data: ClipboardActionData }) => {
    switch (data.data.actionName) {
      case "com.woltlab.wcf.user.enable":
        void enableUsers(data.data.parameters.objectIDs);
        break;
      case "com.woltlab.wcf.user.ban":
        banUsers(data.data.parameters.objectIDs);
        break;
      case "com.woltlab.wcf.user.sendNewPassword":
        sendNewPasswords(data.data.parameters.objectIDs);
        break;
    }
  });

  new AcpUserContentRemoveClipboard();
}

export function setup(hasMarkedItems: boolean) {
  setupUserClipboard(hasMarkedItems);

  new AcpUiUserList();
}
