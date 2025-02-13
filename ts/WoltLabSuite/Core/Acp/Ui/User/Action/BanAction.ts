/**
 * Handles a user ban button.
 *
 * @author  Joshua Ruesweg
 * @copyright  2001-2021 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.5
 */

import * as Core from "../../../../Core";
import AbstractUserAction from "./Abstract";
import BanHandler from "./Handler/Ban";
import * as EventHandler from "../../../../Event/Handler";
import { showDefaultSuccessSnackbar } from "WoltLabSuite/Core/Component/Snackbar";

export class BanAction extends AbstractUserAction {
  private banHandler: BanHandler;

  public constructor(button: HTMLElement, userId: number, userDataElement: HTMLElement) {
    super(button, userId, userDataElement);

    this.banHandler = new BanHandler([this.userId]);

    this.button.addEventListener("click", (event) => {
      event.preventDefault();

      const isBanned = Core.stringToBool(this.userDataElement.dataset.banned!);

      if (isBanned) {
        this.banHandler.unban(() => {
          this.userDataElement.dataset.banned = "false";
          this.button.textContent = this.button.dataset.banMessage!;

          showDefaultSuccessSnackbar();

          EventHandler.fire("com.woltlab.wcf.acp.user", "refresh", {
            userIds: [this.userId],
          });
        });
      } else {
        this.banHandler.ban(() => {
          this.userDataElement.dataset.banned = "true";
          this.button.textContent = this.button.dataset.unbanMessage!;

          showDefaultSuccessSnackbar();

          EventHandler.fire("com.woltlab.wcf.acp.user", "refresh", {
            userIds: [this.userId],
          });
        });
      }
    });
  }
}

export default BanAction;
